<?php

namespace App\Repositories\Contracts;

interface AppointmentSlotRepositoryInterface
{
    public function getAll();

    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function findByIdForUpdate(int $id);
}