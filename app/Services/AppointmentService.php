<?php

namespace App\Services;

use App\Repositories\AppointmentRepository;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Appointment;

class AppointmentService
{
    public function __construct(
        protected AppointmentRepository $appointmentRepository
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

    public function createAppointment(array $data)
    {
        $data['reference_no'] = $this->generateAppointmentReference();
        $data['patient_id'] = auth()->id();

        return $this->appointmentRepository->create($data);
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

    public function cancelAppointment(int $id, array $reason)
    {
        return $this->appointmentRepository->cancel($id, $reason);
    }

    public function rescheduleAppointment(int $id, array $newSlotId)
    {
        return $this->appointmentRepository->reschedule($id, $newSlotId);
    }
}