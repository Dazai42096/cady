@extends('layouts.public')

@section('title', 'طلب عرض سعر - كادي للمولدات والصيانة - CADY EST')

@section('content')
<section class="bg-[#0b192c] text-white py-16 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold">طلب عرض سعر</h1>
    <p class="text-gray-400 mt-3 text-sm md:text-base">يرجى ملء النموذج وسنتواصل معك خلال 24 ساعة عمل</p>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto max-w-2xl px-4">
        
        <div class="bg-gray-50 border border-gray-200 p-8 rounded-2xl shadow-sm">
            
            <form action="{{ route('quote_request.submit') }}" method="POST" class="space-y-6">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-lg text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-bold text-gray-700 mb-1">اسم الشركة أو المؤسسة</label>
                        <input id="company_name" name="company_name" type="text" required value="{{ old('company_name') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white"
                            placeholder="شركة كادي للتجارة">
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-bold text-gray-700 mb-1">الشخص المسؤول (الاسم)</label>
                        <input id="contact_person" name="contact_person" type="text" required value="{{ old('contact_person') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white"
                            placeholder="أحمد علي محمد">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 mb-1">رقم الهاتف</label>
                        <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white"
                            placeholder="0790000000">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white"
                            placeholder="info@cady.com">
                    </div>
                </div>

                <div>
                    <label for="service_type" class="block text-sm font-bold text-gray-700 mb-1">الخدمة المطلوبة</label>
                    <select id="service_type" name="service_type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white">
                        <option value="" disabled selected>اختر الخدمة المطلوبة</option>
                        <option value="buy_generator" {{ old('service_type') == 'buy_generator' ? 'selected' : '' }}>شراء مولد جديد</option>
                        <option value="rent_generator" {{ old('service_type') == 'rent_generator' ? 'selected' : '' }}>تأجير مولد كهربائي</option>
                        <option value="maintenance_contract" {{ old('service_type') == 'maintenance_contract' ? 'selected' : '' }}>عقد صيانة سنوي</option>
                        <option value="spare_parts" {{ old('service_type') == 'spare_parts' ? 'selected' : '' }}>طلب قطع غيار</option>
                        <option value="other" {{ old('service_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-sm font-bold text-gray-700 mb-1">تفاصيل الطلب (الأحمال، مدة الإيجار، إلخ)</label>
                    <textarea id="message" name="message" rows="5" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent text-sm bg-white"
                        placeholder="يرجى كتابة متطلبات المولد والأحمال الكهربائية التقريبية أو الأجهزة التي سيتم تشغيلها..."></textarea>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#0b192c] bg-[#00d26a] hover:bg-[#00b058] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00d26a] transition cursor-pointer">
                        إرسال طلب السعر
                    </button>
                </div>

            </form>

        </div>

    </div>
</section>
@endsection
