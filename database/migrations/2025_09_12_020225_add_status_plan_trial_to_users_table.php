<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add status column if not already present
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('pending')->after('is_deleted');
            }

            // Add plan_id column if not already present
            if (!Schema::hasColumn('users', 'plan_id')) {
                $table->foreignId('plan_id')
                      ->nullable()
                      ->after('status')
                      ->constrained('plans')
                      ->nullOnDelete();
            }

            // Add trial_ends_at column if not already present
            if (!Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')
                      ->nullable()
                      ->after('plan_id');

                // Add index for faster lookups
                $table->index('trial_ends_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'trial_ends_at')) {
                $table->dropIndex(['trial_ends_at']);
                $table->dropColumn('trial_ends_at');
            }

            if (Schema::hasColumn('users', 'plan_id')) {
                $table->dropForeign(['plan_id']);
                $table->dropColumn('plan_id');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
