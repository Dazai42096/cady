<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_mode' => ['required', Rule::in(['existing', 'new'])],
            'customer_id' => ['required_if:customer_mode,existing', 'nullable', 'exists:customers,id'],

            'new_customer.company_name' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:255'],
            'new_customer.contact_person' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:255'],
            'new_customer.phone' => ['required_if:customer_mode,new', 'nullable', 'string', 'max:30'],
            'new_customer.email' => ['nullable', 'email', 'max:255'],
            'new_customer.address' => ['nullable', 'string', 'max:500'],
            'new_customer.business_activity' => ['nullable', 'string', 'max:255'],

            'type' => ['required', Rule::in(['sale', 'rental', 'maintenance_contract', 'spare_parts', 'other'])],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:quotation_date'],
            'project' => ['nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['required', 'numeric', Rule::in([0, 8, 16])],
            'currency' => ['required', Rule::in(['JOD', 'USD'])],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_mode.required' => 'اختر نوع العميل: عميل موجود أو عميل جديد.',
            'customer_id.required_if' => 'اختر العميل من النظام.',
            'new_customer.company_name.required_if' => 'اسم الشركة للعميل الجديد مطلوب.',
            'new_customer.contact_person.required_if' => 'اسم جهة الاتصال للعميل الجديد مطلوب.',
            'new_customer.phone.required_if' => 'رقم الهاتف للعميل الجديد مطلوب.',
            'type.required' => 'نوع عرض السعر مطلوب.',
            'type.in' => 'نوع عرض السعر غير صحيح.',
            'quotation_date.required' => 'تاريخ العرض مطلوب.',
            'valid_until.required' => 'تاريخ انتهاء الصلاحية مطلوب.',
            'valid_until.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ الإصدار.',
            'currency.required' => 'العملة مطلوبة.',
            'tax_rate.in' => 'نسبة الضريبة يجب أن تكون 0 أو 8 أو 16.',
            'items.required' => 'يجب إضافة بند واحد على الأقل.',
            'items.*.description.required' => 'وصف البند مطلوب.',
            'items.*.quantity.required' => 'الكمية مطلوبة.',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر.',
            'items.*.unit_price.required' => 'سعر الوحدة مطلوب.',
        ];
    }
}