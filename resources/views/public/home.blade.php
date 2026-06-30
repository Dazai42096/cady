@extends('layouts.public')

@section('title', 'كادي للمولدات والصيانة - الصفحة الرئيسية - CADY EST')

@section('content')
<section class="bg-gradient-to-br from-[#070f1e] via-[#0b192c] to-[#1e3e62] text-white py-20 px-4">
    <div class="container mx-auto max-w-5xl text-center">
        <span class="bg-[#00d26a]/20 border border-[#00d26a]/40 text-[#00d26a] text-xs px-4 py-1.5 rounded-full font-bold uppercase tracking-wider select-none">حلول الطاقة المتكاملة</span>
        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mt-6">
            توريد وتأجير وصيانة <br>
            <span class="text-[#00d26a]">المولدات الكهربائية</span> الاحترافية
        </h1>
        <p class="text-gray-300 text-lg md:text-xl mt-6 max-w-2xl mx-auto leading-relaxed">
            مؤسسة كادي هي شريكك الموثوق لضمان استمرارية أعمالك دون انقطاع. نوفر أفضل أنواع المولدات الكهربائية وعقود الصيانة الدورية الطارئة في الأردن.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="{{ route('quote_request.form') }}" class="bg-[#00d26a] hover:bg-[#00b058] text-[#0b192c] font-extrabold px-8 py-4 rounded-xl transition shadow-lg w-full sm:w-auto text-center">
                اطلب عرض سعر الآن
            </a>
            <a href="{{ route('services') }}" class="border border-white/20 hover:bg-white/10 text-white font-extrabold px-8 py-4 rounded-xl transition w-full sm:w-auto text-center">
                استعرض خدماتنا
            </a>
        </div>
    </div>
</section>

<section class="py-12 bg-white border-b border-gray-200">
    <div class="container mx-auto max-w-5xl px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
        <div>
            <h3 class="text-4xl font-extrabold text-[#0b192c]">500+</h3>
            <p class="text-sm text-gray-500 mt-2">مولد كهربائي مُورّد</p>
        </div>
        <div>
            <h3 class="text-4xl font-extrabold text-[#0b192c]">150+</h3>
            <p class="text-sm text-gray-500 mt-2">عقد صيانة نشط</p>
        </div>
        <div>
            <h3 class="text-4xl font-extrabold text-[#0b192c]">99.9%</h3>
            <p class="text-sm text-gray-500 mt-2">معدل جاهزية الطاقة</p>
        </div>
        <div>
            <h3 class="text-4xl font-extrabold text-[#0b192c]">24/7</h3>
            <p class="text-sm text-gray-500 mt-2">دعم فني وطوارئ</p>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="container mx-auto max-w-5xl px-4">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-[#0b192c]">خدماتنا المتميزة</h2>
            <p class="text-gray-500 mt-4 max-w-md mx-auto">نقدم خدمات متكاملة تشمل كل ما يتعلق بالمولدات والطاقة المستدامة</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:shadow-xl transition flex flex-col justify-between">
                <div>
                    <div class="text-4xl mb-4">⚙️</div>
                    <h3 class="text-xl font-bold text-[#0b192c]">بيع وتوريد المولدات</h3>
                    <p class="text-sm text-gray-500 mt-3 leading-relaxed">
                        نوفر أفضل المولدات العالمية (Cummins, Perkins) بقدرات تبدأ من 10 ك.ف.أ وحتى 2000 ك.ف.أ بمواصفات عالية وكاتمات صوت متطورة.
                    </p>
                </div>
                <a href="{{ route('services') }}" class="text-[#00d26a] font-bold text-sm mt-6 hover:underline inline-block">اقرأ المزيد ←</a>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:shadow-xl transition flex flex-col justify-between">
                <div>
                    <div class="text-4xl mb-4">🔑</div>
                    <h3 class="text-xl font-bold text-[#0b192c]">تأجير المولدات</h3>
                    <p class="text-sm text-gray-500 mt-3 leading-relaxed">
                        نوفر خيارات إيجار مرنة يومية، أسبوعية، أو سنوية للمشاريع الإنشائية، والفعاليات، والمنشآت الصناعية بأسعار منافسة شاملة الصيانة.
                    </p>
                </div>
                <a href="{{ route('services') }}" class="text-[#00d26a] font-bold text-sm mt-6 hover:underline inline-block">اقرأ المزيد ←</a>
            </div>

            <div class="bg-white p-8 rounded-2xl border border-gray-200 hover:shadow-xl transition flex flex-col justify-between">
                <div>
                    <div class="text-4xl mb-4">🛠️</div>
                    <h3 class="text-xl font-bold text-[#0b192c]">عقود الصيانة الدورية</h3>
                    <p class="text-sm text-gray-500 mt-3 leading-relaxed">
                        عقود صيانة سنوية مخصصة تضمن الكشف الدوري وتغيير الفلاتر والزيوت بمواعيد مجدولة مع استجابة فورية للأعطال الطارئة.
                    </p>
                </div>
                <a href="{{ route('services') }}" class="text-[#00d26a] font-bold text-sm mt-6 hover:underline inline-block">اقرأ المزيد ←</a>
            </div>

        </div>

    </div>
</section>

<section class="bg-[#0b192c] text-white py-16 px-4">
    <div class="container mx-auto max-w-4xl text-center space-y-6">
        <h2 class="text-2xl md:text-4xl font-bold">هل تبحث عن حلول طاقة مضمونة لشركتك؟</h2>
        <p class="text-gray-300 text-sm md:text-base max-w-lg mx-auto leading-relaxed">
            فريق المهندسين الفنيين لدينا مستعد لزيارة موقعك مجاناً وتقدير الأحمال المطلوبة لشركتك وتقديم دراسة وافية.
        </p>
        <div class="pt-4">
            <a href="{{ route('quote_request.form') }}" class="bg-[#00d26a] hover:bg-[#00b058] text-[#0b192c] font-bold px-8 py-4 rounded-xl transition inline-block">
                اطلب دراسة أحمال مجانية وعرض سعر
            </a>
        </div>
    </div>
</section>
@endsection
