<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'          => ['required', 'exists:customers,id'],
            'generator_id'         => ['required', 'exists:generators,id'],
            'to_name'              => ['required', 'string', 'max:255'],
            'project'              => ['nullable', 'string', 'max:255'],
            'contract_start_date'  => ['required', 'date'],
            'contract_end_date'    => ['required', 'date', 'after:contract_start_date'],
            'visit_count'          => ['required', 'integer', 'min:1', 'max:365'],
            'payment_method'       => ['required', 'string', 'max:100'],
            'subtotal'             => ['required', 'numeric', 'min:0'],
            'tax_rate'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'currency'             => ['required', 'string', 'max:10'],
            'terms'                => ['nullable', 'string'],
            'notes'                => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required'         => 'العميل مطلوب',
            'customer_id.exists'           => 'العميل غير موجود',
            'generator_id.required'        => 'المولد مطلوب',
            'generator_id.exists'          => 'المولد غير موجود',
            'to_name.required'             => 'اسم المستلم مطلوب',
            'contract_start_date.required' => 'تاريخ بداية العقد مطلوب',
            'contract_end_date.required'   => 'تاريخ نهاية العقد مطلوب',
            'contract_end_date.after'      => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
            'visit_count.required'         => 'عدد الزيارات مطلوب',
            'visit_count.min'              => 'عدد الزيارات يجب أن يكون على الأقل 1',
            'payment_method.required'      => 'طريقة الدفع مطلوبة',
            'subtotal.required'            => 'قيمة العقد مطلوبة',
            'currency.required'            => 'العملة مطلوبة',
        ];
    }
}
