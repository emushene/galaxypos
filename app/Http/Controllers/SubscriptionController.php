<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Show subscriptions for the logged-in user.
     */
    public function index()
    {
        $user = auth()->user();

    // All subscriptions
    $subscriptions = $user->subscriptions()->with('plan')->latest()->get();

    // Latest subscription (any status)
    $latestSubscription = $user->latestSubscription()->with('plan')->first();

    // Active subscription only
    $activeSubscription = $user->activeSubscription()->with('plan')->first();

    return view('subscriptions.index', compact(
        'subscriptions',
        'latestSubscription',
        'activeSubscription'
    ));
}

    /**
     * Handle a new subscription request.
     */
    public function subscribe(Request $request, Plan $plan)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to subscribe.');
        }

        $user = Auth::user();

        if (!$plan) {
            return redirect()->back()->with('error', 'Selected plan not found.');
        }

        // Create a new subscription record
        $subscription = new Subscription();
        $subscription->user_id = $user->id;
        $subscription->plan_id = $plan->id;
        $subscription->status = 'active'; // Or 'pending' if payment gateway is involved
        $subscription->starts_at = now();

        if ($plan->interval === 'month') {
            $subscription->ends_at = now()->addMonth();
        } elseif ($plan->interval === 'year') {
            $subscription->ends_at = now()->addYear();
        }

        $subscription->save();

        // Update the user's current plan and status
        $user->plan_id = $plan->id;
        $user->status = 'active';
        $user->save();

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription successful!');
    }

    /**
     * Cancel a subscription.
     */
    public function cancel($id)
    {
        $subscription = Subscription::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $subscription->status = 'cancelled';
        $subscription->ends_at = now();
        $subscription->save();

        return redirect()
            ->route('subscriptions.index')
            ->with('success', 'Subscription cancelled successfully.');
    }
}
