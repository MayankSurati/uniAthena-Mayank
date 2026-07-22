<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repositories\DoctorRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentSlotRepository;
use App\Repositories\contract\DoctorRepositoryInterface;
use App\Repositories\contract\AppointmentRepositoryInterface;
use App\Repositories\contract\AppointmentSlotRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AppointmentRepositoryInterface::class,
            AppointmentRepository::class
        );

        $this->app->bind(
            AppointmentSlotRepositoryInterface::class,
            AppointmentSlotRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
