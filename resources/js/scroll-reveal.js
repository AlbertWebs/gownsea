function mark(el, type = 'up', delay = 0) {
    if (!el || el.dataset.revealInit === '1') {
        return;
    }

    el.dataset.revealInit = '1';
    el.classList.add('reveal', `reveal--${type}`);

    if (delay > 0) {
        el.style.setProperty('--reveal-delay', `${delay}s`);
    }
}

function stagger(nodes, type = 'up', step = 0.12) {
    [...nodes].forEach((node, index) => mark(node, type, index * step));
}

export function initScrollReveal() {
    if (document.querySelector('.admin-app')) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const heroRow = document.querySelector('.hero-section > div:first-child');
    if (heroRow) {
        const cols = [...heroRow.children];
        if (cols.length >= 2) {
            mark(cols[0], 'left');
            mark(cols[1], 'right', 0.12);
        } else {
            cols.forEach((col) => mark(col, 'up'));
        }
    }

    document.querySelectorAll('main section').forEach((section) => {
        if (section.classList.contains('page-banner')) {
            return;
        }

        section.querySelectorAll(':scope .max-w-3xl').forEach((header) => {
            mark(header, 'up');
        });

        section.querySelectorAll('.luxury-grid, .grid, .story-band').forEach((grid) => {
            if (grid.closest('.site-footer')) {
                return;
            }

            if (grid.classList.contains('story-band')) {
                const media = grid.querySelector('.story-band__media');
                const copy = grid.querySelector('.story-band__copy');
                mark(media, grid.classList.contains('is-reverse') ? 'right' : 'left');
                mark(copy, grid.classList.contains('is-reverse') ? 'left' : 'right', 0.12);
                return;
            }

            stagger(grid.children, 'up', 0.12);
        });
    });

    stagger(document.querySelectorAll('.site-footer__columns > *'), 'up', 0.1);

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.14,
        rootMargin: '0px 0px -80px 0px',
    });

    document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
}
