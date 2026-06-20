<?php

namespace App\Services;

use App\Repositories\DoctorAvailabilityScheduleRepository;
use App\Models\DoctorAvailabilitySchedule;
use App\Jobs\GenerateAppointmentSlotsJob;
use Illuminate\Support\Facades\DB;

class DoctorAvailabilityScheduleService
{
    public function __construct(
        protected DoctorAvailabilityScheduleRepository $doctorAvailabilityScheduleRepository
    ) {
    }

    public function getDoctors()
    {
        return $this->doctorAvailabilityScheduleRepository->getAll();
    }

    public function getDoctor(int $id)
    {
        return $this->doctorAvailabilityScheduleRepository->findById($id);
    }

    public function createDoctorAvailabilitySchedule(array $data)
    {
        $overlap = DoctorAvailabilitySchedule::query()
            ->where('doctor_id', $data['doctor_id'])
            ->where('date', $data['date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($overlap) {
            return [
                'data' => null,
                'message' => 'Doctor availability already exists for this time range.'
            ];
        }

        // DB::transaction(function () use ($data) {

            $availability = $this->doctorAvailabilityScheduleRepository->create($data);

            GenerateAppointmentSlotsJob::dispatch(
                $availability
            );
            
            return $availability;
        // });
    }

    public function updateDoctor(int $id, array $data)
    {
        return $this->doctorAvailabilityScheduleRepository->update($id, $data);
    }

    public function deleteDoctor(int $id)
    {
        return $this->doctorAvailabilityScheduleRepository->delete($id);
    }
}