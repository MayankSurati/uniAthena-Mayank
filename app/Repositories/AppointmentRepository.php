<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentRescheduled;

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
        return DB::transaction(function () use ($data) {

            $slot = AppointmentSlot::query()
                ->lockForUpdate()
                ->findOrFail($data['appointment_slot_id']);

            if ($slot->status !== 'available') {
            }

            $appointment = Appointment::create([
                'reference_no' => $data['reference_no'],
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'notes' => $data['notes'] ?? null,
                'appointment_slot_id' => $slot->id,
                'status' => 'booked',
            ]);

            $slot->update([
                'status' => 'booked',
            ]);

            $appointment->load([
                'patient',
                'doctor',
                'slot',
            ]);

            AppointmentBooked::dispatch($appointment);

            return $appointment;
        });
    }

    public function update(int $id, array $data)
    {
        $appointment = $this->findById($id);

        $appointment->update($data);

        return $appointment;
    }

    public function delete(int $id)
    {
        $appointment = $this->findById($id);

        return $appointment->delete();
    }

    public function cancel(int $id, array $reason)
    {
        return DB::transaction(function () use ($id, $reason) {

            $appointment = Appointment::with(['patient', 'doctor', 'slot'])->findOrFail($id);

            $oldData = $appointment->toArray();

            $appointment->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason['cancellation_reason'],
                'cancelled_at' => now(),
            ]);

            $appointment->slot->update([
                'status' => 'available'
            ]);

            $appointment->load([
                'patient',
                'doctor',
                'slot',
            ]);

            AppointmentCancelled::dispatch($appointment, $oldData);

            return $appointment;
        });
    }

    public function reschedule(int $id, array $newSlotId)
    {
        return DB::transaction(function () use ($id, $newSlotId) {

            $appointment = Appointment::findOrFail($id);

            $newSlot = AppointmentSlot::query()
                ->lockForUpdate()
                ->findOrFail($newSlotId['appointment_slot_id']);

            if ($newSlot->status !== 'available') 
            {
                
            }

            $oldSlot = AppointmentSlot::query()
                ->lockForUpdate()
                ->findOrFail($appointment->appointment_slot_id);

            $oldSlot->update([
                'status' => 'available'
            ]);

            $newSlot->update([
                'status' => 'booked'
            ]);

            $appointment->update([
                'appointment_slot_id' => $newSlot->id,
                'status' => 'rescheduled'
            ]);

            AppointmentRescheduled::dispatch($appointment);

            return $appointment->fresh();
            
        });
    }
}