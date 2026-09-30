<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\PageController;
use App\Models\JournalPost;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about-us', [PageController::class, 'about'])->name('about-us');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact-us');
Route::get('/legal-attire', [PageController::class, 'legalAttire'])->name('legal-attire');
Route::get('/graduation-attire', [PageController::class, 'graduationAttire'])->name('graduation-attire');
Route::get('/church-wear', [PageController::class, 'churchWear'])->name('church-wear');
Route::get('/gown-for-hire', [PageController::class, 'gownForHire'])->name('gown-for-hire');

Route::get('/the-gown-journal', [PageController::class, 'journalIndex'])->name('journal.index');
Route::get('/the-gown-journal/{slug}', [PageController::class, 'journalShow'])->name('journal.show');

Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/return-policy', [PageController::class, 'returnPolicy'])->name('return-policy');
Route::get('/copyright', [PageController::class, 'copyright'])->name('copyright');

// Shopping & product discovery (URL parity with gownsea.com)
Route::get('/shop-attire/{slug}', [PageController::class, 'shopAttireCollection'])->name('shop-attire.collection');
Route::get('/shop-attire-collection/{mainSlug}/{slug}', [PageController::class, 'shopAttireCategory'])->name('shop-attire.category');
Route::get('/product/{slug}', [PageController::class, 'productShow'])->name('products.show');
Route::get('/our-products/{slug}', [PageController::class, 'ourProduct'])->name('our-products.show');

Route::get('/bulk-inquiry', [PageController::class, 'bulkInquiry'])->name('bulk-inquiry');
Route::get('/terms-and-conditions', [PageController::class, 'termsAndConditions'])->name('terms-and-conditions');

Route::post('/assistant/submit', [AssistantController::class, 'submit'])
    ->middleware('throttle:assistant-submissions')
    ->name('assistant.submit');

Route::get('/sitemap.xml', function (): Response {
    // Keep only canonical, permanent public pages here. Dynamic products and
    // articles are added below from their currently published records.
    $staticRoutes = [
        '/', '/about-us', '/contact-us', '/legal-attire', '/graduation-attire',
        '/church-wear', '/gown-for-hire', '/bulk-inquiry', '/the-gown-journal',
        '/privacy-policy', '/terms-and-conditions', '/return-policy', '/copyright',
    ];

    $urls = collect($staticRoutes)
        ->map(fn (string $path) => [
            'loc' => url($path),
        ])->all();

    $journalUrls = JournalPost::published()->get()->map(fn (JournalPost $post) => [
        'loc' => route('journal.show', $post->slug),
        'lastmod' => ($post->updated_at ?? now())->toDateString(),
    ])->all();

    $productUrls = Product::published()->get()->map(function (Product $product) {
        $path = (string) ($product->url_path ?: route('products.show', $product->slug, false));
        if (! str_starts_with($path, '/') || str_starts_with($path, '//') || in_array(rtrim($path, '/'), [
            '/shop-attire/graduation-attire', '/shop-attire/legal-attire', '/shop-attire/church-wear',
        ], true)) {
            $path = route('products.show', $product->slug, false);
        }

        return [
            'loc' => url($path),
            'lastmod' => ($product->updated_at ?? now())->toDateString(),
        ];
    })->all();

    return response()
        ->view('sitemap', ['urls' => collect(array_merge($urls, $journalUrls, $productUrls))->unique('loc')->values()->all()])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
