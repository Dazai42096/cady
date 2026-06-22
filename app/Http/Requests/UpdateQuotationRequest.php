<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'      => ['required', 'exists:customers,id'],
            'type'             => ['required', 'in:parts,maintenance,installation,other'],
            'quotation_date'   => ['required', 'date'],
            'valid_until'      => ['required', 'date', 'after_or_equal:quotation_date'],
            'project'          => ['nullable', 'string', 'max:255'],
            'discount'         => ['nullable', 'numeric', 'min:0'],
            'tax_rate'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'currency'         => ['required', 'string', 'max:10'],
            'notes'            => ['nullable', 'string'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.qty'      => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required'           => 'العميل مطلوب',
            'customer_id.exists'             => 'العميل المختار غير موجود',
            'type.required'                  => 'نوع عرض السعر مطلوب',
            'quotation_date.required'        => 'تاريخ العرض مطلوب',
            'valid_until.required'           => 'تاريخ انتهاء الصلاحية مطلوب',
            'valid_until.after_or_equal'     => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ الإصدار',
            'currency.required'              => 'العملة مطلوبة',
            'items.required'                 => 'يجب إضافة عنصر واحد على الأقل',
            'items.*.description.required'   => 'وصف البند مطلوب',
            'items.*.qty.required'           => 'الكمية مطلوبة',
            'items.*.unit_price.required'    => 'سعر الوحدة مطلوب',
        ];
    }
}
