<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\AppointmentService;
use App\Http\Requests\AppointmentsRequest;
use App\Http\Requests\CancelAppointmentRequest;
use App\Http\Requests\RescheduleAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\AppointmentListResource;

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
            AppointmentListResource::collection($appointments->items()),
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
            'success' => $appointment['success'],
            'message' => $appointment['message'],
            'data' => $appointment['data']
        ], $appointment['status_code']);
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
            'success' => true,
            'message' => 'Appointment cancelled successfully.',
            'data' => new AppointmentResource($cancel),
        ], 200);
    }

    /**
     * Reschedule appointment the specified Appointment in database.
     */
    public function reschedule(RescheduleAppointmentRequest $rescheduleRequest, int $id)
    {
        $reschedule = $this->appointmentService->rescheduleAppointment($id, $rescheduleRequest->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully',
            'data' => new AppointmentResource($reschedule),
        ], 200);
    }
}
