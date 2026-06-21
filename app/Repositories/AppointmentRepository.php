<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Events\AppointmentCancelled;


class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function getAll()
    {
        return Appointment::query()->latest()->paginate(15);
    }

    public function findById(int $id)
    {
        return Appointment::findOrFail($id);
    }

    public function create(array $data)
    {
        return Appointment::create($data);
    }

    public function update(int $id, array $data)
    {
        $appointment = $this->findById($id);

        return $appointment->update($data);
    }

    public function delete(int $id)
    {
        $appointment = $this->findById($id);

        return $appointment->delete();
    }

    public function cancel(Appointment $appointment, string $reason): bool
    {
        return $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
        ]);
    }

    public function findAppointmentForUpdate(int $id)
    {
        return Appointment::query()
            ->lockForUpdate()
            ->findOrFail($id);
    }

    public function findSlotForUpdate(int $slotId)
    {
        return AppointmentSlot::query()
            ->lockForUpdate()
            ->findOrFail($slotId);
    }

    public function updateSlot(AppointmentSlot $slot,array $data): bool
    {
        return $slot->update($data);
    }

    public function updateAppointment(Appointment $appointment,array $data): bool
    {
        return $appointment->update($data);
    }

    public function findWithRelations(int $id)
    {
        return Appointment::with([
            'patient',
            'doctor',
            'slot'
        ])->findOrFail($id);
    }

    public function refresh(int $id) 
    {
        return Appointment::with([
            'patient',
            'doctor',
            'slot'
        ])->findOrFail($id);
    }
}