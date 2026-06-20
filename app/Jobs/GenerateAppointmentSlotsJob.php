<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DoctorAvailabilitySchedule;
use App\Models\AppointmentSlot;

class GenerateAppointmentSlotsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public DoctorAvailabilitySchedule $schedule)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = $this->schedule->date;

        $start = Carbon::parse("{$date} {$this->schedule->start_time}");
        $end = Carbon::parse("{$date} {$this->schedule->end_time}");

        $duration = $this->schedule->slot_duration;

        $slots = [];

        while ($start->copy()->addMinutes($duration)->lte($end)) {

            $slotEnd = $start->copy()->addMinutes($duration);

            $slots[] = [
                'doctor_id' => $this->schedule->doctor_id,
                'availability_id' => $this->schedule->id,
                'slot_date' => $date,
                'start_at' => $start->format('H:i:s'),
                'end_at' => $slotEnd->format('H:i:s'),
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $start = $slotEnd;
        }

        AppointmentSlot::insert($slots);
    }
}
