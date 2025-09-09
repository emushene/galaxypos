<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class TrialController extends Controller
{
    public function startTrial(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to start a trial.');
        }

        // If user is pending → activate trial
        if ($user->status === 'pending') {
            $user->status = 'active'; // allowed ENUM value
            $user->trial_ends_at = now()->addDays(14); // 14-day trial
            $user->save();

            return redirect()->route('sale.pos')
                ->with('success', 'Your free trial has started!');
        }

        // If trial expired → force to expired page
        if ($user->trial_ends_at && Carbon::now()->greaterThan($user->trial_ends_at)) {
            return redirect()->route('account.trial_expired')
                ->with('error', 'Your trial has expired.');
        }

        // If trial is still active → go to POS
        return redirect()->route('sale.pos');
    }
}
