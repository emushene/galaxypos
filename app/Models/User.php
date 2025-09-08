<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
        'status', // SaaS state (pending, active, suspended, trial_expired, etc.)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isActive()
    {
        return $this->is_active;
    }

    public function holiday()
    {
        return $this->hasMany(Holiday::class);
    }

    // 🔹 Simple role helper
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // 🔹 Permission check helper
    public function hasPermission($permissionName)
    {
        return \DB::table('permissions')
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('role_has_permissions.role_id', $this->role_id)
            ->where('permissions.name', $permissionName)
            ->exists();
    }
}
