<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\AppointmentBookedMail;
use Illuminate\Support\Facades\Mail;

class SendAppointmentBookedEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    // public $queue = 'emails';

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
        Mail::to($event->appointment->patient->email)
            ->queue(new AppointmentBookedMail(
                $event->appointment
            )
        );
    }
}
