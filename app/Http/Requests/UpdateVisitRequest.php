<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'planned_date'      => ['required', 'date'],
            'confirmed_date'    => ['nullable', 'date'],
            'actual_date'       => ['nullable', 'date'],
            'assigned_to'       => ['nullable', 'exists:users,id'],
            'technician_notes'  => ['nullable', 'string'],
            'customer_notes'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'planned_date.required' => 'التاريخ المخطط مطلوب',
            'assigned_to.exists'    => 'الفني المختار غير موجود',
        ];
    }
}
