<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'warehouse_id',
        'start_date',
        'end_date',
        'total_sales',
        'total_purchases',
        'total_expenses',
        'total_returns',
        'total_payments',
        'payment_summary',
        'sales_by_category',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'payment_summary' => 'array',
        'sales_by_category' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
