<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AppointmentHistory;

class CreateAppointmentHistoryListener implements ShouldQueue
{
    // public $queue = 'history';

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
    public function handle(AppointmentBooked $event): void
    {
        AppointmentHistory::create([
            'appointment_id' => $event->appointment->id,
            'action' => 'booked',
            'old_data' => null,
            'new_data' => $event->appointment->toArray(),
            'created_by' => $event->appointment->patient_id
        ]);
    }
}