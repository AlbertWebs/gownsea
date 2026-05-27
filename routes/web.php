<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\PageController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about-us', [PageController::class, 'about'])->name('about-us');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact-us');
Route::get('/legal-attire', [PageController::class, 'legalAttire'])->name('legal-attire');

Route::get('/the-gown-journal', [PageController::class, 'journalIndex'])->name('journal.index');
Route::get('/the-gown-journal/{slug}', [PageController::class, 'journalShow'])->name('journal.show');

Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/return-policy', [PageController::class, 'returnPolicy'])->name('return-policy');
Route::get('/copyright', [PageController::class, 'copyright'])->name('copyright');

// Shopping & product discovery (URL parity with gownsea.com)
Route::get('/shop-attire/{slug}', [PageController::class, 'shopAttireCollection'])->name('shop-attire.collection');
Route::get('/shop-attire-collection/{mainSlug}/{slug}', [PageController::class, 'shopAttireCategory'])->name('shop-attire.category');
Route::get('/our-products/{slug}', [PageController::class, 'ourProduct'])->name('our-products.show');

Route::get('/bulk-inquiry', [PageController::class, 'bulkInquiry'])->name('bulk-inquiry');
Route::get('/terms-and-conditions', [PageController::class, 'termsAndConditions'])->name('terms-and-conditions');

Route::post('/assistant/submit', [AssistantController::class, 'submit'])
    ->middleware('throttle:assistant-submissions')
    ->name('assistant.submit');

Route::get('/sitemap.xml', function (): Response {
    $urls = collect(config('gownsea.protected_routes', []))
        ->map(fn (string $path) => [
            'loc' => url($path),
            'lastmod' => now()->toDateString(),
        ])->all();

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
