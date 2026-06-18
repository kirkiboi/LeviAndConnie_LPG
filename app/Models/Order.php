<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'employee_id', 'subtotal', 'cash_amount', 'gcash_amount',
        'gcash_reference', 'total_amount', 'change_amount', 'status', 'notes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
