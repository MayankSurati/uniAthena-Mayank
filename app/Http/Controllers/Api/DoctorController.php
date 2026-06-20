<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\DoctorService;
use App\Http\Resources\DoctorResource;
use App\Traits\ApiResponse;

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
        return $this->successResponse(
            $doctors,
            DoctorResource::collection($doctors->items()),
            'Doctors Fetched Successfully',
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $doctor = $this->doctorService->getDoctor($id);

        return response()->json([
            'data' => $doctor,
            'message' => 'Doctor details'
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

    public function getSlots(int $doctorId, string $date)
    {
        $slots = $this->doctorService->getDoctorSlots($doctorId, $date);

        return response()->json([
            'data' => $slots,
            'message' => empty($slots) ? 'No slots found' : 'Doctor slots details',
        ]);
    }
}
