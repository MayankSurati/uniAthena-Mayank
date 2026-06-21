<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Models\AppointmentSlot;
use App\Repositories\Contracts\AppointmentSlotRepositoryInterface;

class AppointmentSlotRepository implements AppointmentSlotRepositoryInterface
{
    public function getAll()
    {
        return AppointmentSlot::query()->latest()->paginate(15);
    }

    public function findById(int $id)
    {
        return AppointmentSlot::findOrFail($id);
    }

    public function findByIdForUpdate(int $id)
    {
        return AppointmentSlot::query()
            ->lockForUpdate()
            ->find($id);
    }

    public function create(array $data)
    {
        return AppointmentSlot::create($data);
    }

    public function update(int $id, array $data)
    {
        $appointmentSlot = $this->findById($id);

        return $appointmentSlot->update($data); 
    }

    public function delete(int $id)
    {
        $appointmentSlot = $this->findById($id);

        return $appointmentSlot->delete();
    }
}