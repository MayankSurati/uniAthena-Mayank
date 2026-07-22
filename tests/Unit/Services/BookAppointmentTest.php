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

class BookAppointmentTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_can_book_appointment(): void
    {
        $slot = Mockery::mock(AppointmentSlot::class)->makePartial();

        $slot->id = 1;
        $slot->status = 'available';

        $appointment = Mockery::mock(Appointment::class)->makePartial();

        $appointment
            ->shouldReceive('load')
            ->once()
            ->andReturnSelf();

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentSlotRepository
            ->shouldReceive('findByIdForUpdate')
            ->once()
            ->with(1)
            ->andReturn($slot);

        $appointmentSlotRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                1,
                ['status' => 'booked']
            );

        $appointmentRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($appointment);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $result = $service->createAppointment([
            'patient_id' => 1,
            'doctor_id' => 1,
            'appointment_slot_id' => 1,
        ]);

        $this->assertInstanceOf(
            Appointment::class,
            $result
        );
    }

    public function test_cannot_book_unavailable_slot(): void
    {
        $slot = new AppointmentSlot([
            'id' => 1,
            'status' => 'booked',
        ]);

        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $appointmentSlotRepository
            ->shouldReceive('findByIdForUpdate')
            ->once()
            ->andReturn($slot);

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $this->expectException(
            SlotUnavailableException::class
        );

        $service->createAppointment([
            'patient_id' => 1,
            'doctor_id' => 1,
            'appointment_slot_id' => 1,
        ]);
    }

    public function test_allows_booking_for_today_slot_even_if_start_time_has_passed(): void
    {
        $service = new AppointmentService(
            Mockery::mock(AppointmentRepositoryInterface::class),
            Mockery::mock(AppointmentSlotRepositoryInterface::class)
        );

        $slot = new AppointmentSlot([
            'status' => 'available',
            'slot_date' => now()->toDateString(),
            'start_at' => now()->subHour()->format('H:i:s'),
        ]);

        $method = new \ReflectionMethod(AppointmentService::class, 'assertSlotCanBeBooked');
        $method->setAccessible(true);

        $method->invoke($service, $slot);
    }

    public function test_generates_reference_number(): void
    {
        $appointmentRepository = Mockery::mock(
            AppointmentRepositoryInterface::class
        );

        $appointmentSlotRepository = Mockery::mock(
            AppointmentSlotRepositoryInterface::class
        );

        $service = new AppointmentService(
            $appointmentRepository,
            $appointmentSlotRepository
        );

        $reference = $service->generateAppointmentReference();

        $this->assertMatchesRegularExpression(
            '/^APT-\d{8}-\d{3}$/',
            $reference
        );
    }
}