<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\AppointmentCancelMail;
use Illuminate\Support\Facades\Mail;

class SendAppointmentCancelEmailListener implements ShouldQueue
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
    public function handle(AppointmentCancelled $event): void
    {
        Mail::to($event->appointment->patient->email)
            ->queue(new AppointmentCancelMail(
                $event->appointment
            )
        );
    }
}
