<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
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
        // Placeholder for subscription check logic.
        // In a real application, you would check the user's subscription status in the database.
        // For demonstration purposes, we'll simulate an inactive subscription.
        $subscription_is_active = true; // Change to true to simulate an active subscription

        if (Auth::check() && !$subscription_is_active) {
            // If the user is authenticated but the subscription is not active,
            // redirect them to the inactive account page.
            return redirect()->route('account.inactive');
        }

        return $next($request);
    }
}
