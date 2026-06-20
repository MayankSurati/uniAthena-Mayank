<?php

namespace App\Services;

use App\Repositories\DoctorRepository;

class DoctorService
{
    public function __construct(
        protected DoctorRepository $doctorRepository
    ) {
    }

    public function getDoctors()
    {
        return $this->doctorRepository->getAll();
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

    public function getDoctorSlots(int $id, string $date)
    {
        return $this->doctorRepository->slots($id, $date);
    }
}