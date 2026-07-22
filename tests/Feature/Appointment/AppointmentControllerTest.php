<?php

namespace Tests\Feature\Appointment;

use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Api\AppointmentController;
use App\Services\AppointmentService;
use Illuminate\Http\Response;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    public function test_index_returns_consistent_success_response(): void
    {
        $service = $this->createMock(AppointmentService::class);
        $service->method('getAppointments')->willReturn(collect());

        $controller = new AppointmentController($service);
        $response = $controller->index();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringContainsString('"success":true', $response->getContent());
        $this->assertJsonStringContainsString('"message":"Appointments listing."', $response->getContent());
    }

    public function test_show_returns_success_response_when_appointment_exists(): void
    {
        $service = $this->createMock(AppointmentService::class);
        $service->method('getAppointment')->willReturn((object) [
            'id' => 1,
            'reference_no' => 'APT-20260704-001',
        ]);

        $controller = new AppointmentController($service);
        $response = $controller->show(1);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringContainsString('"success":true', $response->getContent());
        $this->assertJsonStringContainsString('"message":"Appointment details fetched successfully."', $response->getContent());
    }

    public function test_show_throws_slot_exception_for_invalid_state(): void
    {
        $this->expectException(SlotUnavailableException::class);
        $this->expectExceptionMessage('Appointment is no longer available.');

        $service = $this->createMock(AppointmentService::class);
        $service->method('getAppointment')->willThrowException(
            new SlotUnavailableException('Appointment is no longer available.')
        );

        $controller = new AppointmentController($service);
        $controller->show(1);
    }
}
