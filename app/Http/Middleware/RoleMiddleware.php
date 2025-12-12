<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Bypass jika user adalah super_admin dan route meminta akses admin
        if ($user && $user->role === 'super_admin' && in_array('admin', $roles)) {
            return $next($request);
        }

        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}