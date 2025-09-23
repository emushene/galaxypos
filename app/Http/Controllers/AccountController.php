<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display the inactive account page.
     *
     * @return \Illuminate\View\View
     */
    public function inactive()
    {
        return view('account.inactive');
    }

    /**
     * Display the suspended account page.
     *
     * @return \Illuminate\View\View
     */
    public function suspended()
    {
        return view('account.suspended');
    }

    /**
     * Display the pending account page.
     *
     * @return \Illuminate\View\View
     */
    public function pending()
    {
        return view('account.pending');
    }

    /**
     * Display the trial expired page.
     *
     * @return \Illuminate\View\View
     */
    public function trialExpired()
    {
        return view('account.trial_expired');
    }
}
