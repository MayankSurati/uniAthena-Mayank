<?php

namespace App\Listeners;

use App\Events\AppointmentRescheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AppointmentHistory;

class RescheduleAppointmentHistory implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AppointmentRescheduled $event): void
    {
        AppointmentHistory::create([
            'appointment_id' => $event->appointment->id,
            'action' => 'rescheduled',
            'old_data' => null,
            'new_data' => $event->appointment->toArray(),
            'created_by' => $event->appointment->patient_id
        ]);
    }
}
