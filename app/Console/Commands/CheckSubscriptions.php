<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Subscription;
use Carbon\Carbon;

class CheckSubscriptions extends Command
{
    protected $signature = 'subscriptions:check';
    protected $description = 'Check and update expired trials and subscriptions';

    public function handle()
    {
        $now = Carbon::now();

        // 🔹 Expire trials
        User::where('status', 'active')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', $now)
            ->update(['status' => 'trial_expired']);

        // 🔹 Expire subscriptions
        Subscription::where('status', 'active')
            ->where('ends_at', '<', $now)
            ->update(['status' => 'expired']);

        $this->info('Subscription and trial statuses updated.');
    }
}
