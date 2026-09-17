<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => __('Akses ditolak. Halaman ini hanya untuk Administrator IT.')], 403);
            }

            abort(403, __('Akses ditolak. Halaman ini hanya dapat diakses oleh Administrator IT.'));
        }

        return $next($request);
    }
}
