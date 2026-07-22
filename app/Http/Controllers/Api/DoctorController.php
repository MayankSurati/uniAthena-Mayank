<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorSlotRequest;
use App\Http\Resources\BaseCollection;
use App\Services\DoctorService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use ApiResponse;

    public function __construct(protected DoctorService $doctorService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = $this->doctorService->getDoctors();

        return new BaseCollection($doctors, 'Doctors listing.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->errorResponse('Create action is not available.', 405);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = $this->doctorService->getDoctor($id);

        return $this->successResponse($doctor, 'Doctor details fetched successfully.');
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

    public function getSlots(DoctorSlotRequest $request, int $doctorId)
    {
        $slots = $this->doctorService->getDoctorSlots($doctorId, $request->date);

        return new BaseCollection($slots, 'Doctor slots fetched successfully.');
    }
}
