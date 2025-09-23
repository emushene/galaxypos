<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'main';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subdomain',
        'db_database',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
    ];

    /**
     * Get the users associated with the tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
