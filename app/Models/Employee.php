<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Employee extends Model
{
    protected $fillable = [
        'firstName', 'middleName', 'lastName', 'phone',
        'username', 'password', 'role', 'daily_salary', 'isActive'
    ];

    protected $hidden = ['password'];

    public function getFullNameAttribute(): string
    {
        $mid = $this->middleName ? ' ' . $this->middleName . ' ' : ' ';
        return $this->firstName . $mid . $this->lastName;
    }

    public function getShortNameAttribute(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function sessions()
    {
        return $this->hasMany(EmployeeSession::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }
}