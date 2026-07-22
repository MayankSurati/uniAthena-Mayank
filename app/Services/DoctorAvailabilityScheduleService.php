<?php

namespace App\Services;

use App\Jobs\GenerateAppointmentSlotsJob;
use App\Models\DoctorAvailabilitySchedule;
use App\Repositories\DoctorAvailabilityScheduleRepository;

class DoctorAvailabilityScheduleService
{
    public function __construct(
        private readonly DoctorAvailabilityScheduleRepository $doctorAvailabilityScheduleRepository,
    ) {}

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
        if ($this->hasOverlap($data)) {
            return [
                'data' => null,
                'message' => 'Doctor availability already exists for this time range.',
            ];
        }

        $availability = $this->doctorAvailabilityScheduleRepository->create($data);

        GenerateAppointmentSlotsJob::dispatch($availability);

        return $availability;
    }

    public function updateDoctor(int $id, array $data)
    {
        return $this->doctorAvailabilityScheduleRepository->update($id, $data);
    }

    public function deleteDoctor(int $id)
    {
        return $this->doctorAvailabilityScheduleRepository->delete($id);
    }

    private function hasOverlap(array $data): bool
    {
        return DoctorAvailabilitySchedule::query()
            ->where('doctor_id', $data['doctor_id'])
            ->where('date', $data['date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();
    }
}