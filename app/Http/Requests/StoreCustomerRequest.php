<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Policy handles authorization
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_name'      => 'required|string|max:255',
            'contact_person'    => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'email'             => 'required|email|unique:customers,email',
            'address'           => 'nullable|string|max:500',
            'business_activity' => 'nullable|string|max:255',
            'notes'             => 'nullable|string',
        ];
    }

    /**
     * Get custom Arabic validation messages.
     */
    public function messages(): array
    {
        return [
            'company_name.required'   => 'اسم الشركة مطلوب.',
            'company_name.max'        => 'اسم الشركة يجب ألا يتجاوز 255 حرفاً.',
            'contact_person.required' => 'اسم جهة الاتصال مطلوب.',
            'contact_person.max'      => 'اسم جهة الاتصال يجب ألا يتجاوز 255 حرفاً.',
            'phone.required'          => 'رقم الهاتف مطلوب.',
            'phone.max'               => 'رقم الهاتف يجب ألا يتجاوز 20 رقماً.',
            'email.required'          => 'البريد الإلكتروني مطلوب.',
            'email.email'             => 'يرجى إدخال بريد إلكتروني صحيح.',
            'email.unique'            => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'address.max'             => 'العنوان يجب ألا يتجاوز 500 حرف.',
            'business_activity.max'   => 'النشاط التجاري يجب ألا يتجاوز 255 حرفاً.',
        ];
    }
}
