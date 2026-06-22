<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\AppointmentSlotRepositoryInterface;

class CancelAppointmentTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_cancel_appointment(): void
    {
        $appointment = new Appointment();

        $appointment->id = 1;
        $appointment->status = 'booked';
        $appointment->appointment_slot_id = 10;

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentRepository
            ->shouldReceive('findWithRelations')
            ->once()
            ->with(1)
            ->andReturn($appointment);

        $appointmentRepository
            ->shouldReceive('cancel')
            ->once()
            ->with(
                $appointment,
                'Patient requested cancellation'
            );

        $appointmentSlotRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                10,
                ['status' => 'available']
            );

        $appointmentRepository
            ->shouldReceive('refresh')
            ->once()
            ->with(1)
            ->andReturn($appointment);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $result = $service->cancelAppointment(
            1,
            [
                'cancellation_reason' =>
                    'Patient requested cancellation'
            ]
        );

        $this->assertInstanceOf(
            Appointment::class,
            $result
        );
    }

    public function test_cannot_cancel_already_cancelled_appointment(): void
    {
        $appointment = new Appointment();

        $appointment->id = 1;
        $appointment->status = 'cancelled';

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentRepository
            ->shouldReceive('findWithRelations')
            ->once()
            ->with(1)
            ->andReturn($appointment);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $this->expectException(
            \App\Exceptions\SlotUnavailableException::class
        );

        $service->cancelAppointment(
            1,
            [
                'cancellation_reason' => 'Test'
            ]
        );
    }
}