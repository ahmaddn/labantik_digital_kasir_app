<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Allow select-role page, logout, and Livewire internal requests
            if ($request->routeIs('select-role') || $request->is('livewire/*') || $request->is('logout')) {
                return $next($request);
            }

            // Redirect to select-role if context is not in session
            if (! session()->has('active_role_id')) {
                return redirect()->route('select-role');
            }
        }

        return $next($request);
    }
}
