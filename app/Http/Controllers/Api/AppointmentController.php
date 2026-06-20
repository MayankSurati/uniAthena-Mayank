<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\AppointmentService;
use App\Http\Requests\AppointmentsRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Requests\RescheduleAppointmentRequest;

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
        return $this->successResponse(
            $appointments,
            AppointmentResource::collection($appointments->items()),
            'Appointments Fetched Successfully',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppointmentsRequest $appointmentsRequest)
    {
        $appointment = $this->appointmentService->createAppointment($appointmentsRequest->validated());

        return response()->json([
            'success' => $appointment['success'] ?? true,
            'message' => $appointment['message'] ?? 'Success',
            'data' => empty($appointment['data'])
                ? new AppointmentResource($appointment)
                : [],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $viewSlots = $this->appointmentService->getAppointment($id);

        return response()->json([
            'data' => $viewSlots,
            'message' => 'Slots listing'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Cancel the specified Appointment in database.
     */
    public function cancel(CancelAppointmentRequest $cancelRequest, int $id)
    {
        $cancel = $this->appointmentService->cancelAppointment($id, $cancelRequest->validated());

        return response()->json([
            'success' => $cancel['success'] ?? true,
            'message' => $cancel['message'] ?? 'Success',
            'data' => empty($cancel['data'])
                ? new AppointmentResource($cancel)
                : [],
        ]);
    }

    /**
     * Reschedule appointment the specified Appointment in database.
     */
    public function reschedule(RescheduleAppointmentRequest $rescheduleRequest, int $id)
    {
        $reschedule = $this->appointmentService->rescheduleAppointment($id, $rescheduleRequest->validated());

        return response()->json([
            'success' => $reschedule['success'] ?? true,
            'message' => $reschedule['message'] ?? 'Success',
            'data' => empty($reschedule['data'])
                ? new AppointmentResource($reschedule)
                : [],
        ]);
    }
}
