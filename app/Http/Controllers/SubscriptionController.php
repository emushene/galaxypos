<?php

namespace App\Http\Controllers;

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
