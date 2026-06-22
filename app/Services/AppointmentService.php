<?php

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentSlotRepository;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use App\Events\AppointmentBooked;
use App\Events\AppointmentRescheduled;
use App\Events\AppointmentCancelled;
use App\Http\Resources\AppointmentResource;
use App\Exceptions\SlotUnavailableException;

class AppointmentService
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private AppointmentSlotRepository $appointmentSlotRepository
    ) {
    }

    public function getAppointments()
    {
        return $this->appointmentRepository->getAll();
    }

    public function getAppointment(int $id)
    {
        return $this->appointmentRepository->findById($id);
    }

    public function createAppointment(array $data): Appointment
    {
        $appointment = DB::transaction(function () use ($data) {

            $slot = $this->appointmentSlotRepository
                ->findByIdForUpdate($data['appointment_slot_id']);

            if (! $slot) {
                throw new SlotUnavailableException('Slot not found.');
            }

            if ($slot->status !== 'available') {
                throw new SlotUnavailableException(
                    'This slot has already been booked.'
                );
            }

            $appointment = $this->appointmentRepository->create([
                'reference_no' => $this->generateAppointmentReference(),
                'patient_id' => auth()->user()->id,
                'doctor_id' => $data['doctor_id'],
                'appointment_slot_id' => $slot->id,
                'notes' => $data['notes'] ?? null,
                'status' => 'booked',
            ]);

            $this->appointmentSlotRepository->update(
                $slot->id,
                ['status' => 'booked']
            );

            return $appointment->load([
                'patient',
                'doctor',
                'slot'
            ]);
        });

        DB::afterCommit(function () use ($appointment) {
            AppointmentBooked::dispatch($appointment);
        });

        return $appointment;
    }

    public function updateAppointment(int $id, array $data)
    {
        return $this->appointmentRepository->update($id, $data);
    }

    public function deleteAppointment(int $id)
    {
        return $this->appointmentRepository->delete($id);
    }

    public function generateAppointmentReference(): string
    {
        $date = Carbon::today()->format('Ymd');

        $lastAppointment = Appointment::whereDate('created_at', today())
            ->latest('id')
            ->first();

        $sequence = $lastAppointment
            ? ((int) substr($lastAppointment->reference_no, -3)) + 1
            : 1;

        return sprintf(
            'APT-%s-%03d',
            $date,
            $sequence
        );
    }

    public function cancelAppointment(int $id, array $data)
    {
        $appointment = $this->appointmentRepository
        ->findWithRelations($id);

        if ($appointment->status === 'cancelled') {
            throw new SlotUnavailableException(
                'Appointment already cancelled.'
            );
        }

        $appointment = DB::transaction(function () use ($appointment, $data) {

            $this->appointmentRepository->cancel(
                $appointment,
                $data['cancellation_reason']
            );
            
            $this->appointmentSlotRepository->update(
                $appointment->appointment_slot_id,
                [
                    'status' => 'available'
                ]
            );

            return $this->appointmentRepository->refresh(
                $appointment->id
            );
        });

        AppointmentCancelled::dispatch($appointment);

        return $appointment;
    }

    public function rescheduleAppointment(int $appointmentId, array $data)
    {
        return DB::transaction(function () use ($appointmentId, $data) {

            $appointment = $this->appointmentRepository
                ->findAppointmentForUpdate($appointmentId);

            $newSlot = $this->appointmentRepository
                ->findSlotForUpdate(
                    $data['appointment_slot_id']
                );

            if ($newSlot->status !== 'available')
            {
                throw new SlotUnavailableException(
                    'The selected appointment slot is no longer available. Please choose another slot.'
                );
            }

            if ($appointment->appointment_slot_id === $newSlot->id)
            {
                throw new SlotUnavailableException(
                    'Appointment is already assigned to this slot.'
                );
            }

            $oldSlot = $this->appointmentRepository
                ->findSlotForUpdate($appointment->appointment_slot_id);

            $this->appointmentRepository->updateSlot(
                $oldSlot,
                ['status' => 'available']
            );

            $this->appointmentRepository->updateSlot(
                $newSlot,
                ['status' => 'booked']
            );

            $this->appointmentRepository->updateAppointment(
                $appointment,
                [
                    'appointment_slot_id' => $newSlot->id,
                    'status' => 'rescheduled',
                ]
            );

            $appointment->fresh([
                'patient',
                'doctor',
                'slot'
            ]);

            AppointmentRescheduled::dispatch($appointment);

            return $appointment;    
        });
    }
}