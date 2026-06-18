<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['date', 'description', 'amount', 'type', 'reference_id'];

    protected $casts = ['date' => 'date'];
}
