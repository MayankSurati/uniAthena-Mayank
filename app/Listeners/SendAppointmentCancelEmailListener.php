<?php

namespace App\Listeners;

use App\Events\AppointmentCancel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\AppointmentBookedMail;
use Illuminate\Support\Facades\Mail;

class SendAppointmentCancelEmail implements ShouldQueue
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
    public function handle(AppointmentCancel $event): void
    {
        Mail::to($event->appointment->patient->email)
            ->queue(new AppointmentBookedMail(
                $event->appointment
            )
        );
    }
}
