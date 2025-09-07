<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable =[

        "name", "company_name", "license_number",
        "email", "phone_number", "valid_start", "valid_end",
        "user_id", "is_active",

    ];
}
