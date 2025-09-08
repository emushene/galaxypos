<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PreventCashierFromDashboard
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role) {
            $role = Auth::user()->role->name;

            // Only block cashiers
            if ($role === 'Cashier') {
                // If cashier is trying to access dashboard/home, send them to POS
                if ($request->is('dashboard') || $request->is('home') || $request->is('/')) {
                    return redirect()->route('sale.pos')
                        ->with('warning', 'Cashiers do not have access to the dashboard.');
                }
            }
        }

        return $next($request);
    }
}
