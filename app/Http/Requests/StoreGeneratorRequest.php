<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneratorRequest extends FormRequest
{
    /**
     * Authorization is handled by the policy, not here.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for storing a new generator.
     */
    public function rules(): array
    {
        return [
            'customer_id'   => ['required', 'uuid', 'exists:customers,id'],
            'serial_number' => ['required', 'string', 'max:100', 'unique:generators,serial_number'],
            'model'         => ['required', 'string', 'max:100'],
            'brand'         => ['required', 'string', 'max:100'],
            'capacity_kva'  => ['required', 'numeric', 'min:0'],
            'fuel_type'     => ['required', 'string', 'in:diesel,gas,dual'],
            'location'      => ['nullable', 'string', 'max:255'],
            'status'        => ['required', 'string', 'in:available,rented,maintenance,inactive'],
            'notes'         => ['nullable', 'string'],
        ];
    }

    /**
     * Arabic validation messages.
     */
    public function messages(): array
    {
        return [
            'customer_id.required'   => 'يجب اختيار العميل.',
            'customer_id.exists'     => 'العميل المختار غير موجود في النظام.',
            'serial_number.required' => 'الرقم التسلسلي مطلوب.',
            'serial_number.max'      => 'الرقم التسلسلي يجب ألا يتجاوز 100 حرف.',
            'serial_number.unique'   => 'هذا الرقم التسلسلي مسجّل مسبقاً لمولّد آخر.',
            'model.required'         => 'موديل المولد مطلوب.',
            'model.max'              => 'الموديل يجب ألا يتجاوز 100 حرف.',
            'brand.required'         => 'الماركة مطلوبة.',
            'brand.max'              => 'الماركة يجب ألا تتجاوز 100 حرف.',
            'capacity_kva.required'  => 'القدرة بـ KVA مطلوبة.',
            'capacity_kva.numeric'   => 'القدرة يجب أن تكون رقماً.',
            'capacity_kva.min'       => 'القدرة يجب أن تكون قيمة موجبة.',
            'fuel_type.required'     => 'نوع الوقود مطلوب.',
            'fuel_type.in'           => 'نوع الوقود يجب أن يكون: ديزل، غاز، أو ثنائي.',
            'location.max'           => 'الموقع يجب ألا يتجاوز 255 حرفاً.',
            'status.required'        => 'حالة المولد مطلوبة.',
            'status.in'              => 'حالة المولد المختارة غير صالحة.',
        ];
    }
}
