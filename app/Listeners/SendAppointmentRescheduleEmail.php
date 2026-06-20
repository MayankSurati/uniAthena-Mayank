<?php

namespace App\Listeners;

use App\Events\AppointmentReschedule;
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
    public function handle(AppointmentReschedule $event): void
    {
        Mail::to($event->appointment->patient->email)
            ->queue(new AppointmentRescheduleMail(
                $event->appointment
            )
        );
    }
}
