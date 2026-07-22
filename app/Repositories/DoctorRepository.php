<?php

namespace App\Repositories;

use App\Models\AppointmentSlot;
use App\Models\Doctor;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DoctorRepository implements DoctorRepositoryInterface
{
    public function getAll(int $perPage = 100): LengthAwarePaginator
    {
        return Doctor::query()
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'is_active',
            ])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id)
    {
        return Doctor::query()->findOrFail($id);
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

    public function slots(int $doctorId, string $date): LengthAwarePaginator
    {
        return AppointmentSlot::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('slot_date', $date)
            ->where('status', 'available')
            ->orderBy('start_at')
            ->select([
                'id',
                'start_at',
                'end_at',
                'status',
            ])
            ->paginate(100);
    }
}