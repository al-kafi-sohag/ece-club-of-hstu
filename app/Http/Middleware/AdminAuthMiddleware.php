<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            flash()->error('You are not authorized to access this page.');
            return redirect()->route('backend.auth.login');
        }
        return $next($request);
    }
}
