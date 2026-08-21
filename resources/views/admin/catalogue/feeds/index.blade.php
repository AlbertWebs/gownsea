@extends('layouts.admin')
@section('title', 'Catalogue feeds')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-zinc-500">Dashboard / Catalogue / Feeds</p>
            <h1>Catalogue feeds</h1>
            <p class="mt-1 text-sm text-zinc-600">
                Export published products for Google Merchant Center and Meta (Facebook) catalogues.
            </p>
        </div>
        <p class="rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm text-zinc-600">
            <span class="font-semibold text-zinc-900">{{ number_format($productCount) }}</span> published products
        </p>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="admin-card space-y-4">
            <div>
                <h2>Google Merchant Center</h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Product feed with Google fields (`id`, `title`, `link`, `image_link`, `availability`, `price`, and more).
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('admin.catalogue.feeds.export', ['channel' => 'google', 'format' => 'csv']) }}"
                    class="btn-primary"
                >Export CSV</a>
                <a
                    href="{{ route('admin.catalogue.feeds.export', ['channel' => 'google', 'format' => 'excel']) }}"
                    class="btn-secondary"
                >Export Excel</a>
            </div>
            @if ($googlePreview->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-zinc-200">
                    <table class="admin-table min-w-[520px]">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($googlePreview as $row)
                                <tr>
                                    <td class="font-mono text-xs">{{ $row['id'] }}</td>
                                    <td>{{ $row['title'] }}</td>
                                    <td>{{ $row['price'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin-card space-y-4">
            <div>
                <h2>Facebook / Meta catalogue</h2>
                <p class="mt-1 text-sm text-zinc-500">
                    Feed for Meta Commerce Manager / Facebook product catalogues and pixel matching.
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('admin.catalogue.feeds.export', ['channel' => 'facebook', 'format' => 'csv']) }}"
                    class="btn-primary"
                >Export CSV</a>
                <a
                    href="{{ route('admin.catalogue.feeds.export', ['channel' => 'facebook', 'format' => 'excel']) }}"
                    class="btn-secondary"
                >Export Excel</a>
            </div>
            @if ($facebookPreview->isNotEmpty())
                <div class="overflow-x-auto rounded-xl border border-zinc-200">
                    <table class="admin-table min-w-[520px]">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facebookPreview as $row)
                                <tr>
                                    <td class="font-mono text-xs">{{ $row['id'] }}</td>
                                    <td>{{ $row['title'] }}</td>
                                    <td>{{ $row['price'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <section class="admin-card mt-6 space-y-2">
        <h2>How to use</h2>
        <ul class="list-disc space-y-1 pl-5 text-sm text-zinc-600">
            <li>Only <strong>published + public</strong> products are included.</li>
            <li>Upload the Google CSV/Excel into <strong>Google Merchant Center → Products → Add products → Upload</strong>.</li>
            <li>Upload the Facebook CSV/Excel into <strong>Meta Commerce Manager → Catalogue → Add items → Upload file</strong>.</li>
            <li>Prices use your website currency setting (default KES). Products without a price export as <code>0.00</code>.</li>
        </ul>
    </section>
@endsection
