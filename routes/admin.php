<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureAdminAuthenticated;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
        Route::get('forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
        Route::post('forgot-password', [AuthController::class, 'sendReset'])->name('password.email');
        Route::get('reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
        Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update');
    });

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':dashboard'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('search', SearchController::class)->name('search');
        Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
        Route::get('notifications/{notification}', [NotificationController::class, 'read'])->name('notifications.read');
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':catalogue'])->prefix('catalogue')->name('catalogue.')->group(function () {
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::post('products/{product}/toggle/{field}', [ProductController::class, 'toggle'])->name('products.toggle');
        Route::resource('categories', CategoryController::class)->except(['show']);
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':inquiries'])->group(function () {
        Route::get('inquiries/products', [InquiryController::class, 'products'])->name('inquiries.products');
        Route::get('inquiries/general', [InquiryController::class, 'general'])->name('inquiries.general');
        Route::get('inquiries/export', [InquiryController::class, 'export'])->name('inquiries.export');
        Route::get('inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::patch('inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
        Route::post('inquiries/{inquiry}/notes', [InquiryController::class, 'note'])->name('inquiries.note');
        Route::post('inquiries/{inquiry}/convert', [InquiryController::class, 'convert'])->name('inquiries.convert');
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':leads'])->group(function () {
        Route::get('leads/pipeline', [LeadController::class, 'pipeline'])->name('leads.pipeline');
        Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
        Route::post('leads/{lead}/move', [LeadController::class, 'move'])->name('leads.move');
        Route::post('leads/{lead}/convert-sale', [LeadController::class, 'convertSale'])->name('leads.convert-sale');
        Route::resource('leads', LeadController::class)->except(['destroy']);
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':customers'])->group(function () {
        Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
        Route::resource('customers', CustomerController::class)->except(['destroy']);
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':sales'])->group(function () {
        Route::get('sales/export', [SaleController::class, 'export'])->name('sales.export');
        Route::resource('sales', SaleController::class)->except(['destroy', 'edit']);
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':activities'])->group(function () {
        Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':reports'])->get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':users'])->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    });

    Route::middleware(['auth', EnsureAdminAuthenticated::class.':settings'])->group(function () {
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
