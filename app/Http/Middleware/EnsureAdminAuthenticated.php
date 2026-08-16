<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            return redirect()->route('admin.login');
        }

        if ($permission && ! $user->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
