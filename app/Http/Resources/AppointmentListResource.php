<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_no' => $this->reference_no,
            'status' => $this->status,

            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->name,
            ],

            'patient' => [
                'id' => $this->patient->id,
                'name' => $this->patient->name,
                'email' => $this->patient->email,
            ],

            'slot' => [
                'date' => $this->slot->slot_date,
                'start_time' => \Carbon\Carbon::parse($this->slot->start_at)
                    ->format('h:i A'),
                'end_time' => \Carbon\Carbon::parse($this->slot->end_at)
                    ->format('h:i A'),
            ],

            'created_at' => $this->created_at,
        ];
    }
}