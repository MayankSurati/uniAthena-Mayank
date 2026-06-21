<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorAvailabilityScheduleController;
use App\Http\Controllers\Api\AppointmentController;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware([
    'auth:sanctum',
    'role:admin'
    ])->group(function () {
        
        Route::apiResource('doctors', DoctorController::class);

        Route::get('doctors/{doctor}/{date}', [DoctorController::class, 'getSlots']);

        Route::apiResource('doctor-availability', DoctorAvailabilityScheduleController::class);

        Route::apiResource('appointment', AppointmentController::class);

        Route::post('appointment/{doctor}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('appointment/{doctor}/reschedule', [AppointmentController::class, 'reschedule']);
});

Route::middleware([
    'auth:sanctum',
    'role:admin'
    ])->group(function () {
        
        Route::apiResource('doctors', DoctorController::class);

        Route::get('doctors/{doctor}/{date}', [DoctorController::class, 'getSlots']);

        Route::apiResource('doctor-availability', DoctorAvailabilityScheduleController::class);

        Route::apiResource('appointment', AppointmentController::class);
});

Route::middleware([
    'auth:sanctum',
    'role:patient'
    ])->group(function () {

        Route::get('doctors/{doctor}/{date}', [DoctorController::class, 'getSlots']);
        
        Route::apiResource('appointment', AppointmentController::class);

        Route::post('appointment/{doctor}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('appointment/{doctor}/reschedule', [AppointmentController::class, 'reschedule']);
});


