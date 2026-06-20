<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentHistory extends Model
{
    protected $fillable = [
        'appointment_id',
        'action',
        'old_data',
        'new_data',
        'created_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];
}
