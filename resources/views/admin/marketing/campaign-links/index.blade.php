@extends('layouts.admin')
@section('title', 'Social funnels')
@section('content')
    <section class="space-y-6">
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.16em] text-zinc-500">Marketing / Campaign tools</p>
                <h1 class="mt-1 text-3xl font-extrabold tracking-tight text-[#0f2744]">Social funnels</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-600">Create trackable links for social posts, creator partnerships, and paid ads. Choose the page people should land on, then copy the finished URL into your ad.</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 text-right shadow-sm">
                <strong class="block text-xl text-[#0f2744]">{{ number_format($links->total()) }}</strong>
                <span class="text-xs font-medium text-zinc-600">saved campaign links</span>
            </div>
        </header>

        @if ($createdLink)
            <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm" aria-labelledby="created-link-heading">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Ready to use</p>
                        <h2 id="created-link-heading" class="mt-1 text-lg font-bold text-emerald-950">{{ $createdLink->name }}</h2>
                        <p class="mt-1 text-sm text-emerald-900">Copy this tracked URL into your social media ad or post.</p>
                    </div>
                    <a class="text-sm font-bold text-emerald-900 underline underline-offset-4" href="{{ $createdLink->trackedUrl() }}" target="_blank" rel="noopener noreferrer">Open landing page</a>
                </div>
                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <input class="admin-input min-w-0 flex-1 border-emerald-300 bg-white font-mono text-xs" readonly aria-label="New tracked campaign URL" value="{{ $createdLink->trackedUrl() }}">
                    <button type="button" class="btn-primary shrink-0" data-copy-text="{{ $createdLink->trackedUrl() }}">Copy campaign link</button>
                </div>
            </section>
        @endif

        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(20rem,.9fr)]">
            <section class="admin-card border-zinc-200 p-5 sm:p-6">
                <div class="mb-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#d42127]">Build a funnel link</p>
                    <h2 class="mt-1 text-xl font-bold text-[#0f2744]">Campaign details</h2>
                    <p class="mt-1 text-sm text-zinc-600">UTM parameters tell analytics which platform, campaign, and ad brought in each visit.</p>
                </div>
                <form method="POST" action="{{ route('admin.marketing.store') }}" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    <label class="block text-sm font-semibold text-zinc-800 sm:col-span-2">
                        Internal link name
                        <input class="admin-input mt-1.5" name="name" required maxlength="120" value="{{ old('name') }}" placeholder="e.g. October graduation tassels offer">
                        @error('name')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-zinc-800 sm:col-span-2">
                        Landing page
                        <select class="admin-input mt-1.5" name="destination" required>
                            <option value="">Choose where the ad should land</option>
                            @foreach ($destinations as $key => $destination)
                                <option value="{{ $key }}" @selected(old('destination') === $key)>{{ $destination['label'] }}</option>
                            @endforeach
                        </select>
                        @error('destination')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-zinc-800">
                        Social platform
                        <select class="admin-input mt-1.5" name="utm_source" required>
                            @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'youtube' => 'YouTube', 'whatsapp' => 'WhatsApp', 'linkedin' => 'LinkedIn', 'x' => 'X', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('utm_source', 'instagram') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('utm_source')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-zinc-800">
                        Traffic type
                        <select class="admin-input mt-1.5" name="utm_medium" required>
                            @foreach (['paid_social' => 'Paid social ad', 'organic_social' => 'Organic social post', 'influencer' => 'Creator / influencer'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('utm_medium', 'paid_social') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('utm_medium')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-zinc-800 sm:col-span-2">
                        Campaign name
                        <input class="admin-input mt-1.5" name="utm_campaign" required maxlength="190" value="{{ old('utm_campaign') }}" placeholder="e.g. 2026-nairobi-graduation">
                        <span class="mt-1 block text-xs font-normal text-zinc-600">Use one consistent name for every ad in the same campaign.</span>
                        @error('utm_campaign')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-zinc-800">
                        Ad / post label <span class="font-normal text-zinc-500">(optional)</span>
                        <input class="admin-input mt-1.5" name="utm_content" maxlength="190" value="{{ old('utm_content') }}" placeholder="e.g. video-blue-gown">
                        @error('utm_content')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="block text-sm font-semibold text-zinc-800">
                        Audience / keyword <span class="font-normal text-zinc-500">(optional)</span>
                        <input class="admin-input mt-1.5" name="utm_term" maxlength="190" value="{{ old('utm_term') }}" placeholder="e.g. university-graduates">
                        @error('utm_term')<span class="mt-1 block text-xs font-medium text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-200 pt-4 sm:col-span-2">
                        <p class="max-w-lg text-xs leading-5 text-zinc-600">Use the generated link as the ad’s website URL. Published product pages and key landing pages are available above.</p>
                        <button type="submit" class="btn-primary">Generate tracked link</button>
                    </div>
                </form>
            </section>

            <aside class="space-y-5">
                <section class="admin-card border-zinc-200 p-5">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#d42127]">How the funnel is tracked</p>
                    <ol class="mt-4 grid gap-4">
                        @foreach ([['1', 'Choose the landing page', 'Send each audience straight to a relevant product, hire, or enquiry page.'], ['2', 'Copy the campaign URL', 'Paste the generated URL into the website link field in your ad or post.'], ['3', 'Review attributed visitors', 'The saved link shows visitors attributed to its platform, campaign, and ad label.']] as [$step, $title, $copy])
                            <li class="flex gap-3">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-[#0f2744] text-xs font-bold text-white">{{ $step }}</span>
                                <span><strong class="block text-sm text-zinc-900">{{ $title }}</strong><span class="mt-0.5 block text-xs leading-5 text-zinc-600">{{ $copy }}</span></span>
                            </li>
                        @endforeach
                    </ol>
                    <p class="mt-4 rounded-lg bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-950">Visitor counts use first-party session tracking and begin when a generated link receives visits. They are not the same as the click totals reported by an ad platform.</p>
                </section>
                <section class="rounded-xl border border-sky-200 bg-sky-50 p-4">
                    <h3 class="text-sm font-bold text-sky-950">Want to run Meta ads?</h3>
                    <p class="mt-1 text-xs leading-5 text-sky-900">Use Facebook or Instagram as the platform and select “Paid social ad.” A Meta Pixel is a separate tracking setup; these links provide campaign attribution and do not install a Pixel.</p>
                </section>
            </aside>
        </div>

        <section class="admin-card overflow-hidden border-zinc-200 p-0">
            <div class="flex flex-wrap items-end justify-between gap-3 border-b border-zinc-200 px-5 py-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#d42127]">Your saved links</p>
                    <h2 class="mt-1 text-xl font-bold text-[#0f2744]">Campaign library</h2>
                </div>
                <span class="text-xs text-zinc-600">Visitors are attributed after the tagged landing page loads.</span>
            </div>
            <div class="overflow-x-auto">
                <table class="admin-table min-w-[1050px]">
                    <thead><tr><th>Campaign / destination</th><th>Platform</th><th>Ad label</th><th>Visitors</th><th>Tracked URL</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse ($links as $link)
                            <tr>
                                <td>
                                    <strong class="block text-zinc-900">{{ $link->name }}</strong>
                                    <span class="admin-table__meta">{{ $link->destination_label }} · {{ str_replace('_', ' ', $link->utm_medium) }}</span>
                                    <span class="admin-table__meta">Campaign: {{ $link->utm_campaign }}</span>
                                </td>
                                <td class="font-semibold capitalize">{{ $link->utm_source }}</td>
                                <td>{{ $link->utm_content ?: '—' }}</td>
                                <td class="font-bold text-[#0f2744]">{{ number_format($link->tracked_visitors) }}</td>
                                <td><input class="admin-input w-[25rem] font-mono text-xs" readonly aria-label="Tracked URL for {{ $link->name }}" value="{{ $link->trackedUrl() }}"></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="btn-secondary btn-sm" data-copy-text="{{ $link->trackedUrl() }}">Copy</button>
                                        <a class="btn-navy btn-sm" href="{{ $link->trackedUrl() }}" target="_blank" rel="noopener noreferrer">Open</a>
                                        <form method="POST" action="{{ route('admin.marketing.destroy', $link) }}" onsubmit="return confirm('Remove this saved campaign link?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-danger btn-sm">Remove</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="admin-table__empty"><p>No campaign links yet</p><p>Create a tracked link above, then copy it into your ad.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($links->hasPages())<div class="border-t border-zinc-200 px-5 py-4">{{ $links->links() }}</div>@endif
        </section>
    </section>

    <script>
        document.addEventListener('click', async (event) => {
            const button = event.target.closest('[data-copy-text]');
            if (!button) return;
            const original = button.textContent;
            try {
                await navigator.clipboard.writeText(button.dataset.copyText);
            } catch (_) {
                const field = document.createElement('textarea');
                field.value = button.dataset.copyText;
                field.style.position = 'fixed';
                field.style.opacity = '0';
                document.body.appendChild(field);
                field.select();
                document.execCommand('copy');
                field.remove();
            }
            button.textContent = 'Copied';
            window.setTimeout(() => button.textContent = original, 1800);
        });
    </script>
@endsection
