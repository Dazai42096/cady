<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home');
    }

    public function about()
    {
        return view('public.about');
    }

    public function services()
    {
        return view('public.services');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function showQuoteRequestForm()
    {
        return view('public.quote-request');
    }

    public function submitQuoteRequest(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'service_type' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'company_name.required' => 'اسم الشركة مطلوب.',
            'contact_person.required' => 'اسم الشخص المسؤول مطلوب.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'service_type.required' => 'نوع الخدمة مطلوب.',
            'message.required' => 'تفاصيل الرسالة مطلوبة.',
        ]);

        try {
            $quoteRequest = QuoteRequest::create(array_merge($validated, [
                'status' => 'pending',
            ]));

            // Log audit log for anonymous/guest submission
            app(\App\Services\AuditLogService::class)->log(
                action: 'quote_request.submit',
                entityType: QuoteRequest::class,
                entityId: $quoteRequest->id,
                newValues: $quoteRequest->only(['company_name', 'email', 'service_type'])
            );

            return back()->with('success', 'تم إرسال طلب عرض السعر بنجاح! سيقوم فريق المبيعات لدينا بالاتصال بك قريباً.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء إرسال طلبك. يرجى المحاولة لاحقاً.']);
        }
    }
}
