<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    /**
     * Show the support form
     */
    public function showForm()
    {
        return view('support.form');
    }

    /**
     * Handle support request submission
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
            // Guests can submit too
            'email'   => Auth::check() ? 'nullable|email' : 'required|email'
        ]);

        $user = Auth::user();

        // Collect useful context for support
        $context = [
            'user_id'      => $user->id ?? 'Guest',
            'name'         => $user->name ?? 'Guest User',
            'email'        => $user->email ?? $request->email,
            'account_status' => $user->account_status ?? 'Unknown',
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->header('User-Agent'),
            'submitted_at' => now()->toDateTimeString(),
        ];

        // === 1) Send email to support team ===
        Mail::send('support.email-template', [
            'subject'        => $request->subject,
            'messageContent' => $request->message,
            'context'        => $context,
        ], function ($mail) use ($request, $context) {
            $mail->to('support@yourdomain.com') // change to real support inbox
                 ->subject('[Support Request] ' . $request->subject)
                 ->replyTo($context['email'], $context['name']);
        });

        // === 2) Send confirmation email to user (optional, but recommended) ===
        Mail::send('support.confirmation-template', [
            'name'    => $context['name'],
            'subject' => $request->subject,
        ], function ($mail) use ($context) {
            $mail->to($context['email'], $context['name'])
                 ->subject('✅ We’ve received your support request');
        });

        return back()->with('success', '✅ Your message has been sent to our support team. We’ll get back to you soon.');
    }
}
