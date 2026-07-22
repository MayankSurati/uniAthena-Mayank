<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'appointment_slot_id' => [
                'required',
                'integer',
                Rule::exists('appointment_slots', 'id')->where(fn ($query) => $query->where('doctor_id', $this->input('doctor_id'))),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
