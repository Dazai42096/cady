@extends('layouts.public')

@section('title', 'اتصل بنا - كادي للمولدات والصيانة - CADY EST')

@section('content')
<section class="bg-[#0b192c] text-white py-16 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold">اتصل بنا</h1>
    <p class="text-gray-400 mt-3 text-sm md:text-base">تواصل مع قسم المبيعات والصيانة والدعم الفني</p>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto max-w-4xl px-4 grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div class="space-y-8">
            <h2 class="text-2xl font-bold text-[#0b192c] border-r-4 border-[#00d26a] pr-4">تفاصيل الاتصال</h2>
            <p class="text-gray-500 text-sm leading-relaxed">
                يسعدنا الرد على جميع استفساراتكم المتعلقة بتوريد المولدات الكهربائية وصيانتها وحساب الأحمال الكهربائية لمنشآتكم. تواصلوا معنا بالطريقة الأنسب لكم.
            </p>

            <div class="space-y-4">
                <div class="flex items-start space-x-4 space-x-reverse">
                    <span class="text-2xl p-3 bg-gray-50 border border-gray-200 rounded-xl">📞</span>
                    <div>
                        <h4 class="font-bold text-[#0b192c]">الهاتف المجاني والمبيعات</h4>
                        <p class="text-sm text-gray-500">0790000000 / 065000000</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 space-x-reverse">
                    <span class="text-2xl p-3 bg-gray-50 border border-gray-200 rounded-xl">✉️</span>
                    <div>
                        <h4 class="font-bold text-[#0b192c]">البريد الإلكتروني العام</h4>
                        <p class="text-sm text-gray-500">info@cady-est.com / sales@cady-est.com</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 space-x-reverse">
                    <span class="text-2xl p-3 bg-gray-50 border border-gray-200 rounded-xl">📍</span>
                    <div>
                        <h4 class="font-bold text-[#0b192c]">موقع المعرض والإدارة</h4>
                        <p class="text-sm text-gray-500">الأردن، عمان، المنطقة الصناعية</p>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <a href="https://wa.me/962790000000" target="_blank" class="inline-flex items-center space-x-2 space-x-reverse bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-4 rounded-xl transition shadow-md">
                    <span class="text-xl">💬</span>
                    <span>راسلنا مباشرة عبر واتساب</span>
                </a>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 p-8 rounded-2xl shadow-sm space-y-6">
            <h3 class="text-xl font-bold text-[#0b192c]">أوقات العمل الرسمية</h3>
            <ul class="text-sm text-gray-500 space-y-3">
                <li class="flex justify-between border-b border-gray-200 pb-2">
                    <span>من السبت إلى الخميس:</span>
                    <span class="font-semibold text-slate-800">8:00 صباحاً - 5:00 مساءً</span>
                </li>
                <li class="flex justify-between border-b border-gray-200 pb-2">
                    <span>الجمعة:</span>
                    <span class="font-semibold text-red-500">عطلة رسمية</span>
                </li>
                <li class="flex justify-between">
                    <span>دعم طوارئ وصيانة الأعطال:</span>
                    <span class="font-semibold text-green-600">على مدار 24 ساعة (عبر الخط الساخن)</span>
                </li>
            </ul>
            
            <hr class="border-gray-200">
            
            <div>
                <h4 class="font-bold text-[#0b192c] mb-2">ملاحظة هامة للعملاء:</h4>
                <p class="text-xs text-gray-500 leading-relaxed">
                    عملائنا الكرام الحاصلين على عقود صيانة سنوية سارية المفعول، يرجى استخدام بوابة العملاء للإبلاغ عن الأعطال وحجز زيارات الصيانة الطارئة للحصول على الاستجابة الفورية خلال أقل من 4 ساعات.
                </p>
            </div>
        </div>

    </div>
</section>
@endsection
