<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentsRequest;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Requests\RescheduleAppointmentRequest;
use App\Http\Resources\AppointmentListResource;
use App\Http\Resources\AppointmentResource;
use App\Services\AppointmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function __construct(protected AppointmentService $appointmentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = $this->appointmentService->getAppointments();

        return AppointmentListResource::collection($appointments)
            ->additional([
                'success' => true,
                'message' => 'Appointments listing.',
            ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentsRequest $appointmentsRequest)
    {
        $appointment = $this->appointmentService->createAppointment($appointmentsRequest->validated());

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Appointment booked successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = $this->appointmentService->getAppointment((int) $id);

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Appointment details fetched successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->errorResponse('Update action is not available.', 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->errorResponse('Delete action is not available.', 405);
    }

    /**
     * Cancel the specified appointment in storage.
     */
    public function cancel(CancelAppointmentRequest $cancelRequest, int $id)
    {
        $appointment = $this->appointmentService->cancelAppointment($id, $cancelRequest->validated());

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Appointment cancelled successfully.'
        );
    }

    /**
     * Reschedule the specified appointment in storage.
     */
    public function reschedule(RescheduleAppointmentRequest $rescheduleRequest, int $id)
    {
        $appointment = $this->appointmentService->rescheduleAppointment($id, $rescheduleRequest->validated());

        return $this->successResponse(
            new AppointmentResource($appointment),
            'Appointment rescheduled successfully.'
        );
    }
}