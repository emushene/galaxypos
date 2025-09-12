<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'role_id',
        'biller_id',
        'warehouse_id',
        'is_active',
        'is_deleted',
        'status',   // SaaS state (pending, active, suspended, trial_expired, etc.)
        'plan_id',  // quick reference to currently assigned plan
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasPermission(string $permissionName): bool
    {
        return DB::table('permissions')
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('role_has_permissions.role_id', $this->role_id)
            ->where('permissions.name', $permissionName)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    // Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Holidays
    public function holiday()
    {
        return $this->hasMany(Holiday::class);
    }

    // Current assigned plan (quick access)
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Full subscription history
    public function subscriptions()
    {

        return $this->hasMany(Subscription::class, 'user_id');
    }

    // Most recent subscription (could be active, trial, expired, etc.)
    // Removed duplicate currentSubscription method to avoid redeclaration error.

    // Currently active subscription
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | SaaS / Subscription Helpers
    |--------------------------------------------------------------------------
    */
    // Check if user is on trial
    public function onTrial(): bool
    {
        return $this->subscriptions()
            ->where('status', 'trial')
            ->where('trial_ends_at', '>=', now())
            ->exists();
    }

    // Check if user has any active subscription
    public function subscribed(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->exists();
    }

    /**
     * Get the user's most recent subscription (regardless of status).
     */
    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }



    /**
     * Get the user's current active subscription.
     */
    public function currentSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            });
    }
}
