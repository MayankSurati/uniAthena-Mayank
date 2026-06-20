<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AppointmentHistory;

class CancelAppointmentHistoryListener implements ShouldQueue
{
    // public $queue = 'cancel';

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
    public function handle(AppointmentCancelled $event): void
    {
        AppointmentHistory::create([
            'appointment_id' => $event->appointment->id,
            'action' => 'cancelled',
            'old_data' => $event->oldData,
            'new_data' => [
                'status' => $event->appointment->status,
                'cancellation_reason' => $event->appointment->cancellation_reason,
            ],
            'created_by' => $event->appointment->patient_id
        ]);
    }
}