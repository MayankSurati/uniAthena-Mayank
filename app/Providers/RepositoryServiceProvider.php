<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\DoctorRepositoryInterface;
use App\Repositories\DoctorRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DoctorRepositoryInterface::class,
            DoctorRepository::class
        );
    }
}