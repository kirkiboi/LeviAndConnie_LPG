<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSession extends Model
{
    protected $fillable = ['employee_id', 'date', 'time_in', 'time_out'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getHoursWorkedAttribute(): ?float
    {
        if (!$this->time_out) return null;
        $in  = strtotime($this->time_in);
        $out = strtotime($this->time_out);
        return round(($out - $in) / 3600, 2);
    }
}
