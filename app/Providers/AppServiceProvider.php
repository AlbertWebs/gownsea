<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\AdminNotification;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\Sale;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('assistant-submissions', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perHour(20)->by($request->ip()),
            ];
        });

        ResetPassword::createUrlUsing(function (object $user, string $token) {
            return url('/admin/reset-password/'.$token.'?email='.urlencode($user->email));
        });

        View::composer('layouts.admin', function ($view) {
            $view->with('adminBadges', [
                'inquiries' => Inquiry::query()->where('status', 'new')->count(),
                'leads' => Lead::query()->whereNull('assigned_to')->whereNotIn('stage', ['won', 'lost'])->count(),
                'activities' => Activity::query()->where('status', 'pending')->where('due_at', '<', now())->count(),
                'sales' => Sale::query()->whereIn('status', ['pending', 'confirmed'])->count(),
            ]);
            $view->with('adminNotifications', AdminNotification::query()->latest()->limit(8)->get());
            $view->with('unreadNotifications', AdminNotification::query()->whereNull('read_at')->count());
        });
    }
}
