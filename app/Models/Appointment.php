<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Doctor;
use App\Models\AppointmentSlot;
use App\Models\AppointmentHistory;

class Appointment extends Model
{
    protected $fillable = [
        'reference_no',
        'doctor_id',
        'patient_id',
        'appointment_slot_id',
        'status',
        'notes',
        'cancelled_at',
        'cancellation_reason',
        'created_at',
        'updated_at',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function slot()
    {
        return $this->belongsTo(AppointmentSlot::class,'appointment_slot_id');
    }

    public function histories()
    {
        return $this->hasMany(AppointmentHistory::class);
    }
}
