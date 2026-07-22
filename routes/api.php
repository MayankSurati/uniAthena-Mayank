<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorAvailabilityScheduleController;
use App\Http\Controllers\Api\DoctorController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Route::get('login', function () {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Unauthenticated.',
    //         'errors' => [
    //             'auth' => ['Unauthenticated.'],
    //         ],
    //     ], 401);
    // })->name('login');

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('doctors', DoctorController::class);
        Route::apiResource('doctor-availability', DoctorAvailabilityScheduleController::class);
        Route::get('doctor/{doctor}/slots', [DoctorController::class, 'getSlots']);
    });
    
    Route::middleware('role:patient')->group(function () {
        Route::apiResource('appointments', AppointmentController::class);
        Route::post('appointment/{doctor}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('appointment/{doctor}/reschedule', [AppointmentController::class, 'reschedule']);
    });
});