<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        Log::info('RedirectIfAuthenticated middleware triggered', [
            'url'             => $request->fullUrl(),
            'guard'           => $guard,
            'is_authenticated'=> Auth::guard($guard)->check(),
            'user_id'         => Auth::check() ? Auth::id() : null,
            'role'            => Auth::check() && Auth::user()->role ? Auth::user()->role->name : null,
        ]);

        if (Auth::guard($guard)->check()) {
            $user = Auth::user();

            // 🚀 Force Cashiers (and Sales) to POS
            if ($user->role && in_array($user->role->name, ['Cashier', 'Sales'])) {
                Log::info('RedirectIfAuthenticated sending user to /pos', [
                    'user_id' => $user->id,
                    'role'    => $user->role->name,
                ]);
                return redirect('/pos');
            }

            // 🚀 All other roles go to Dashboard
            Log::info('RedirectIfAuthenticated sending user to /dashboard', [
                'user_id' => $user->id,
                'role'    => $user->role ? $user->role->name : 'N/A',
            ]);
            return redirect('/dashboard');
        }

        Log::info('RedirectIfAuthenticated passing request through (guest)', [
            'url' => $request->fullUrl(),
        ]);

        return $next($request);
    }
}
