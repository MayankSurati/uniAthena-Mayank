<?php

namespace App\Repositories;

use App\Models\DoctorAvailabilitySchedule;
use App\Repositories\Contracts\DoctorAvailabilityScheduleRepositoryInterface;

class DoctorAvailabilityScheduleRepository implements DoctorAvailabilityScheduleRepositoryInterface
{
    public function getAll()
    {
        return DoctorAvailabilitySchedule::query()->latest()->paginate(15);
    }

    public function findById(int $id)
    {
        return DoctorAvailabilitySchedule::query()->findOrFail($id);
    }

    public function create(array $data)
    {
        return DoctorAvailabilitySchedule::create($data);
    }

    public function update(int $id, array $data)
    {
        $doctorAvailabilitySchedule = $this->findById($id);
        $doctorAvailabilitySchedule->update($data);

        return $doctorAvailabilitySchedule;
    }

    public function delete(int $id)
    {
        $doctorAvailabilitySchedule = $this->findById($id);

        return $doctorAvailabilitySchedule->delete();
    }
}