<?php

namespace App\Listeners;

use App\Events\AppointmentRescheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\AppointmentRescheduleMail;
use Illuminate\Support\Facades\Mail;

class SendAppointmentRescheduleEmail implements ShouldQueue
{
    use InteractsWithQueue;

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
        Mail::to($event->appointment->patient->email)
            ->queue(new AppointmentRescheduleMail(
                $event->appointment
            )
        );
    }
}
