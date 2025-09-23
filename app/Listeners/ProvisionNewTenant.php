<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProvisionNewTenant implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $webhookUrl = env('N8N_PROVISIONING_WEBHOOK_URL');

        if (!$webhookUrl) {
            Log::error('N8N_PROVISIONING_WEBHOOK_URL is not set. Cannot provision new tenant.');
            return;
        }

        try {
            $response = Http::post($webhookUrl, [
                'user' => [
                    'id' => $event->user->id,
                    'email' => $event->user->email,
                    'name' => $event->user->name,
                ],
            ]);

            if ($response->failed()) {
                Log::error('Failed to trigger n8n provisioning webhook.', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'user_id' => $event->user->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while triggering n8n provisioning webhook.', [
                'message' => $e->getMessage(),
                'user_id' => $event->user->id,
            ]);
        }
    }
}