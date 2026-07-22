<?php

namespace App\Services;

use App\Repositories\Contracts\DoctorRepositoryInterface;

class DoctorService
{
    public function __construct(
        private readonly DoctorRepositoryInterface $doctorRepository,
    ) {}

    public function getDoctors(int $perPage = 100)
    {
        return $this->doctorRepository->getAll($perPage);
    }

    public function getDoctor(int $id)
    {
        return $this->doctorRepository->findById($id);
    }

    public function createDoctor(array $data)
    {
        return $this->doctorRepository->create($data);
    }

    public function updateDoctor(int $id, array $data)
    {
        return $this->doctorRepository->update($id, $data);
    }

    public function deleteDoctor(int $id)
    {
        return $this->doctorRepository->delete($id);
    }

    public function getDoctorSlots(int $doctorId, string $date)
    {
        return $this->doctorRepository->slots($doctorId, $date);
    }
}