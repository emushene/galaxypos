<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Active
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->status) {
                case 'active':
                    // Let them through
                    return $next($request);

                case 'pending':
                    Auth::logout();
                    return redirect()->route('account.pending');

                case 'trial_expired':
                    Auth::logout();
                    return redirect()->route('account.trialExpired');

                case 'suspended':
                    Auth::logout();
                    return redirect()->route('account.suspended');

                case 'deleted':
                default:
                    Auth::logout();
                    return redirect()->route('account.deleted');
            }
        }

        return $next($request);
    }
}
