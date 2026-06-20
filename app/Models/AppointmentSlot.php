<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSlot extends Model
{
    protected $fillable = [
        'doctor_id',
        'availability_id',
        'email',
        'start_at',
        'end_at',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getFormattedTimeAttribute(): string
    {
        return \Carbon\Carbon::parse($this->start_at)->format('h:i A')
            .' - '.
            \Carbon\Carbon::parse($this->end_at)->format('h:i A');
    }
}
