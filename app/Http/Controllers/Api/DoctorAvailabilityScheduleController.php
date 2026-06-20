<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Services\DoctorAvailabilityScheduleService;
use App\Http\Requests\DoctorAvailabilityScheduleRequest;

class DoctorAvailabilityScheduleController extends Controller
{
    use ApiResponse;

    public function __construct(protected DoctorAvailabilityScheduleService $doctorAvailabilityScheduleService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = $this->doctorAvailabilityScheduleService->getDoctors();
        return $this->successResponse(
            $doctors,
            DoctorResource::collection($doctors->items()),
            'Doctors Fetched Successfully',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DoctorAvailabilityScheduleRequest $doctorAvailabilityScheduleRequest)
    {
        $availability  = $this->doctorAvailabilityScheduleService->createDoctorAvailabilitySchedule($doctorAvailabilityScheduleRequest->validated());

        return response()->json([
            'data' => $availability,
            'message' => $availability['message'] ?? 'Doctors Availability Schedule Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $viewSlots = $this->doctorAvailabilityScheduleService->getDoctors($id);

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
}
