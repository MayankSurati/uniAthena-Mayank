<?php

namespace App\Services;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentRescheduled;
use App\Exceptions\SlotUnavailableException;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentSlotRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepository $appointmentRepository,
        private readonly AppointmentSlotRepository $appointmentSlotRepository,
    ) {}

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
        $appointment = DB::transaction(function () use ($data): Appointment {
            $slot = $this->appointmentSlotRepository->findSlotForDoctorForUpdate(
                $data['appointment_slot_id'],
                $data['doctor_id']
            );

            $this->assertSlotCanBeBooked($slot);

            $appointment = $this->appointmentRepository->create([
                'reference_no' => $this->generateAppointmentReference(),
                'patient_id' => auth()->id(),
                'doctor_id' => $data['doctor_id'],
                'appointment_slot_id' => $slot->id,
                'notes' => $data['notes'] ?? null,
                'status' => 'booked',
            ]);

            $this->appointmentSlotRepository->update($slot->id, ['status' => 'booked']);

            return $appointment->load(['patient', 'doctor', 'slot']);
        });

        AppointmentBooked::dispatch($appointment);

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
        $lastAppointment = Appointment::query()
            ->whereDate('created_at', today())
            ->latest('id')
            ->first();

        $sequence = $lastAppointment
            ? ((int) substr($lastAppointment->reference_no, -3)) + 1
            : 1;

        return sprintf('APT-%s-%03d', $date, $sequence);
    }

    public function cancelAppointment(int $id, array $data)
    {
        $appointment = $this->appointmentRepository->findWithRelations($id);

        if ($appointment->status === 'cancelled') {
            throw new SlotUnavailableException('Appointment already cancelled.');
        }

        $appointment = DB::transaction(function () use ($appointment, $data): Appointment {
            $this->appointmentRepository->cancel($appointment, $data['cancellation_reason']);
            $this->appointmentSlotRepository->update($appointment->appointment_slot_id, ['status' => 'available']);

            return $this->appointmentRepository->refresh($appointment->id);
        });

        AppointmentCancelled::dispatch($appointment);

        return $appointment;
    }

    public function rescheduleAppointment(int $appointmentId, array $data)
    {
        return DB::transaction(function () use ($appointmentId, $data): Appointment {
            $appointment = $this->appointmentRepository->findAppointmentForUpdate($appointmentId);
            $newSlot = $this->appointmentRepository->findSlotForUpdate($data['appointment_slot_id']);

            $this->assertSlotCanBeRescheduled($appointment, $newSlot);

            $oldSlot = $this->appointmentRepository->findSlotForUpdate($appointment->appointment_slot_id);

            $this->appointmentRepository->updateSlot($oldSlot, ['status' => 'available']);
            $this->appointmentRepository->updateSlot($newSlot, ['status' => 'booked']);
            $this->appointmentRepository->updateAppointment($appointment, [
                'appointment_slot_id' => $newSlot->id,
                'status' => 'rescheduled',
            ]);

            $appointment->refresh();
            $appointment->load(['patient', 'doctor', 'slot']);

            AppointmentRescheduled::dispatch($appointment);

            return $appointment;
        });
    }

    private function assertSlotCanBeBooked(?object $slot): void
    {
        if (! $slot) {
            throw new SlotUnavailableException('Selected slot does not belong to the selected doctor.');
        }

        if ($slot->status !== 'available') {
            throw new SlotUnavailableException('This slot has already been booked.');
        }

        $slotDate = Carbon::parse($slot->slot_date)->startOfDay();
        $today = Carbon::today()->startOfDay();

        if ($slotDate->lt($today)) {
            throw new SlotUnavailableException('You cannot book an appointment for a past date.');
        }
    }

    private function assertSlotCanBeRescheduled(Appointment $appointment, object $newSlot): void
    {
        if ($newSlot->status !== 'available') {
            throw new SlotUnavailableException('The selected appointment slot is no longer available. Please choose another slot.');
        }

        if ($appointment->appointment_slot_id === $newSlot->id) {
            throw new SlotUnavailableException('Appointment is already assigned to this slot.');
        }
    }
}