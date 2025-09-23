<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'main';

    protected $fillable = [
        "name",
        "description",
        "guard_name",
        "is_active",
    ];

    // Each role can have many users
    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
