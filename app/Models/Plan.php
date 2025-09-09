<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'interval', // monthly, yearly, etc.
        'registers',
        'users',
        'inventory_management',
        'reports',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // A plan can be assigned to many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A plan can have many subscriptions (full SaaS style)
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
