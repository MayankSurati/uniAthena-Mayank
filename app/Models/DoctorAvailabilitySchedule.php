<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAvailabilitySchedule extends Model
{
    /** @use HasFactory<\Database\Factories\DoctorAvailabilityScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'slot_duration',
    ];
}
