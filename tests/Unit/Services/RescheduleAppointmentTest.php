<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Mockery;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Services\AppointmentService;
use App\Exceptions\SlotUnavailableException;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\AppointmentSlotRepositoryInterface;

class RescheduleAppointmentTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_reschedule_appointment(): void
    {
        $appointment = Mockery::mock(Appointment::class)->makePartial();

        $appointment->id = 1;
        $appointment->appointment_slot_id = 10;

        $appointment
            ->shouldReceive('fresh')
            ->once()
            ->andReturnSelf();

        $oldSlot = new AppointmentSlot();
        $oldSlot->id = 10;
        $oldSlot->status = 'booked';

        $newSlot = new AppointmentSlot();
        $newSlot->id = 20;
        $newSlot->status = 'available';

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentRepository
            ->shouldReceive('findSlotForUpdate')
            ->once()
            ->with(20)
            ->andReturn($newSlot);

        $appointmentRepository
            ->shouldReceive('findSlotForUpdate')
            ->once()
            ->with(10)
            ->andReturn($oldSlot);

        $appointmentRepository
            ->shouldReceive('updateSlot')
            ->once()
            ->with(
                $oldSlot,
                ['status' => 'available']
            )
            ->andReturn(true);

        $appointmentRepository
            ->shouldReceive('updateSlot')
            ->once()
            ->with(
                $newSlot,
                ['status' => 'booked']
            )
            ->andReturn(true);

        $appointmentRepository
            ->shouldReceive('updateAppointment')
            ->once()
            ->with(
                $appointment,
                [
                    'appointment_slot_id' => 20,
                    'status' => 'rescheduled',
                ]
            )
            ->andReturn(true);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $result = $service->rescheduleAppointment(
            1,
            [
                'appointment_slot_id' => 20,
            ]
        );

        $this->assertInstanceOf(
            Appointment::class,
            $result
        );
    }

    public function test_cannot_reschedule_same_slot(): void
    {
        $appointment = new Appointment();

        $appointment->id = 1;
        $appointment->appointment_slot_id = 10;

        $slot = new AppointmentSlot();

        $slot->id = 10;
        $slot->status = 'available';

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentRepository
            ->shouldReceive('findAppointmentForUpdate')
            ->once()
            ->with(1)
            ->andReturn($appointment);

        $appointmentRepository
            ->shouldReceive('findSlotForUpdate')
            ->once()
            ->with(10)
            ->andReturn($slot);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $this->expectException(
            \App\Exceptions\SlotUnavailableException::class
        );

        $this->expectExceptionMessage(
            'Appointment is already assigned to this slot.'
        );

        $service->rescheduleAppointment(
            1,
            [
                'appointment_slot_id' => 10
            ]
        );
    }

    public function test_cannot_reschedule_to_booked_slot(): void
    {
        $appointment = new Appointment();

        $appointment->id = 1;
        $appointment->appointment_slot_id = 10;

        $newSlot = new AppointmentSlot();

        $newSlot->id = 20;
        $newSlot->status = 'booked';

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentRepository
            ->shouldReceive('findAppointmentForUpdate')
            ->once()
            ->with(1)
            ->andReturn($appointment);

        $appointmentRepository
            ->shouldReceive('findSlotForUpdate')
            ->once()
            ->with(20)
            ->andReturn($newSlot);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $this->expectException(
            \App\Exceptions\SlotUnavailableException::class
        );

        $this->expectExceptionMessage(
            'The selected appointment slot is no longer available. Please choose another slot.'
        );

        $service->rescheduleAppointment(
            1,
            [
                'appointment_slot_id' => 20
            ]
        );
    }
}