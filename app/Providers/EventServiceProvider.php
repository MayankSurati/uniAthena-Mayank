<?php

namespace App\Providers;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentResscheduled;

use App\Listeners\CreateAppointmentHistoryListener;
use App\Listeners\SendAppointmentBookedEmailListener;

use App\Listeners\RescheduleAppointmentHistory;
use App\Listeners\SendAppointmentRescheduleEmail;

use App\Listeners\CancelAppointmentHistoryListener;
use App\Listeners\SendAppointmentCancelledEmailListener;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        
        AppointmentBooked::class => [
            CreateAppointmentHistoryListener::class,
            SendAppointmentBookedEmailListener::class,
        ],

        AppointmentCancelled::class => [
            CancelAppointmentHistoryListener::class,
            SendAppointmentCancelledEmailListener::class,
        ],

        AppointmentResscheduled::class => [
            RescheduleAppointmentHistory::class,
            SendAppointmentRescheduleEmail::class,
        ],
    ];
}