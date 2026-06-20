<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Models\AppointmentSlot;
use App\Repositories\Contracts\DoctorRepositoryInterface;

class DoctorRepository implements DoctorRepositoryInterface
{
    public function getAll()
    {
        return Doctor::query()->latest()->paginate(15);
    }

    public function findById(int $id)
    {
        return Doctor::findOrFail($id);
    }

    public function create(array $data)
    {
        return Doctor::create($data);
    }

    public function update(int $id, array $data)
    {
        $doctor = $this->findById($id);

        $doctor->update($data);

        return $doctor;
    }

    public function delete(int $id)
    {
        $doctor = $this->findById($id);

        return $doctor->delete();
    }

    public function slots(int $id, string $date)
    {
        return AppointmentSlot::query()
        ->where('doctor_id', $id)
        ->where('slot_date', $date)
        ->where('status', 'available')
        ->orderBy('start_at')
        ->select('id', 'start_at', 'end_at', 'status')
        ->get();
    }
}