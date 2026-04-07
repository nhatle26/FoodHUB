<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentPath = $request->path();

            switch ($user->role) {
                case 'admin':
                    if (!str_starts_with($currentPath, 'admin')) {
                        return redirect('/admin');
                    }
                    break;
                case 'shop':
                    if (!str_starts_with($currentPath, 'shop')) {
                        return redirect('/shop');
                    }
                    break;
                case 'customer':
                    if (!str_starts_with($currentPath, 'dashboard') && $currentPath !== '/') {
                        return redirect('/dashboard');
                    }
                    break;
            }
        }

        return $next($request);
    }
}