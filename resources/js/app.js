import './bootstrap';
import Alpine from 'alpinejs';

Alpine.data('dropzone', (config = {}) => ({
    multiple: Boolean(config.multiple),
    hover: false,
    error: '',
    files: [],
    urls: [],
    maxSize: 4 * 1024 * 1024,

    addFromInput(event) {
        this.addFiles(event.target.files);
    },

    addFiles(fileList) {
        this.error = '';
        const incoming = Array.from(fileList || []).filter((file) => file.type.startsWith('image/'));

        if (! incoming.length) {
            this.error = 'Please drop image files only.';
            return;
        }

        const oversized = incoming.find((file) => file.size > this.maxSize);
        if (oversized) {
            this.error = 'Each image must be 4MB or smaller.';
            return;
        }

        const next = this.multiple ? [...this.files, ...incoming] : incoming.slice(0, 1);
        this.urls.forEach((url) => URL.revokeObjectURL(url));
        this.files = next;
        this.urls = next.map((file) => URL.createObjectURL(file));
        this.syncInput();
    },

    remove(index) {
        URL.revokeObjectURL(this.urls[index]);
        this.files.splice(index, 1);
        this.urls.splice(index, 1);
        this.syncInput();
    },

    syncInput() {
        const transfer = new DataTransfer();
        this.files.forEach((file) => transfer.items.add(file));
        this.$refs.input.files = transfer.files;
    },
}));

Alpine.data('richEditor', () => ({
    init() {
        const boot = () => {
            if (! window.Quill) {
                window.setTimeout(boot, 40);
                return;
            }

            const quill = new window.Quill(this.$refs.editor, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link'],
                        ['clean'],
                    ],
                },
            });

            const initial = this.$refs.input.value || '';
            if (initial) {
                quill.root.innerHTML = initial;
            }

            const sync = () => {
                const html = quill.root.innerHTML;
                this.$refs.input.value = html === '<p><br></p>' ? '' : html;
            };

            quill.on('text-change', sync);
            this.$el.closest('form')?.addEventListener('submit', sync);
        };

        boot();
    },
}));

Alpine.data('pipelineBoard', (config) => ({
    columns: config.columns,
    stages: config.stages,
    moveUrl: config.moveUrl,
    csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
    draggingId: null,
    overStage: null,
    error: '',

    cards(stage) {
        return this.columns[stage] || [];
    },

    count(stage) {
        return this.cards(stage).length;
    },

    totalValue(stage) {
        return this.cards(stage).reduce((sum, card) => sum + (Number(card.value) || 0), 0);
    },

    formatKes(amount) {
        return 'KES ' + Number(amount || 0).toLocaleString();
    },

    findStage(id) {
        return this.stages.find((stage) => this.cards(stage).some((card) => String(card.id) === String(id)));
    },

    dragStart(id, event) {
        this.draggingId = id;
        this.error = '';
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(id));
    },

    dragEnd() {
        this.draggingId = null;
        this.overStage = null;
    },

    async dropOn(stage, event) {
        event.preventDefault();
        const id = event.dataTransfer.getData('text/plain') || this.draggingId;
        this.overStage = null;
        await this.move(id, stage);
    },

    async move(id, stage) {
        if (!id || !stage) {
            return;
        }

        const from = this.findStage(id);
        if (!from || from === stage) {
            return;
        }

        const card = this.cards(from).find((item) => String(item.id) === String(id));
        this.columns[from] = this.cards(from).filter((item) => String(item.id) !== String(id));
        this.columns[stage] = [card, ...this.cards(stage)];

        try {
            const response = await fetch(`${this.moveUrl}/${id}/move`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrf,
                },
                body: JSON.stringify({ stage }),
            });

            if (!response.ok) {
                throw new Error('Move failed');
            }
        } catch (error) {
            this.columns[stage] = this.cards(stage).filter((item) => String(item.id) !== String(id));
            this.columns[from] = [card, ...this.cards(from)];
            this.error = 'Could not move that lead. Please try again.';
        }
    },
}));

window.Alpine = Alpine;
Alpine.start();
