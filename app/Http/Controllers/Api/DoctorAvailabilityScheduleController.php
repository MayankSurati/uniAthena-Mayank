<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorAvailabilityScheduleRequest;
use App\Services\DoctorAvailabilityScheduleService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DoctorAvailabilityScheduleController extends Controller
{
    use ApiResponse;

    public function __construct(protected DoctorAvailabilityScheduleService $doctorAvailabilityScheduleService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $availabilitySchedules = $this->doctorAvailabilityScheduleService->getDoctors();

        return $this->successResponse(
            $availabilitySchedules,
            'Doctors availability schedules fetched successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DoctorAvailabilityScheduleRequest $doctorAvailabilityScheduleRequest)
    {
        $availability = $this->doctorAvailabilityScheduleService->createDoctorAvailabilitySchedule(
            $doctorAvailabilityScheduleRequest->validated()
        );

        return $this->successResponse(
            $availability,
            $availability['message'] ?? 'Doctor availability schedule created successfully.'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $availability = $this->doctorAvailabilityScheduleService->getDoctors($id);

        return $this->successResponse($availability, 'Doctor availability details fetched successfully.');
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
}
