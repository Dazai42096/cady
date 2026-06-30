@extends('layouts.public')

@section('title', 'خدماتنا - كادي للمولدات والصيانة - CADY EST')

@section('content')
<section class="bg-[#0b192c] text-white py-16 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold">خدماتنا وحلولنا</h1>
    <p class="text-gray-400 mt-3 text-sm md:text-base">نوفر حلول طاقة كهربائية متكاملة لجميع القطاعات</p>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto max-w-4xl px-4 space-y-16">
        
        <div class="flex flex-col md:flex-row items-center gap-12 border-b border-gray-150 pb-12">
            <div class="md:w-1/3 text-6xl text-center md:text-right">⚙️</div>
            <div class="md:w-2/3 space-y-4">
                <h3 class="text-2xl font-bold text-[#0b192c]">توريد وتركيب المولدات الكهربائية الجديدة</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    نحن متخصصون في استيراد وتوريد كبرى المولدات الكهربائية العالمية مثل محركات Cummins ومحركات Perkins. نقدم دراسة وافية للأحمال لضمان اختيار الحجم المناسب لمنشأتك مع تركيب لوحات التحويل التلقائية (ATS) وأنظمة العزل الصوتي والاهتزاز.
                </p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-12 border-b border-gray-150 pb-12">
            <div class="md:w-1/3 text-6xl text-center md:text-right">🔑</div>
            <div class="md:w-2/3 space-y-4">
                <h3 class="text-2xl font-bold text-[#0b192c]">تأجير المولدات الكهربائية الصامتة</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    نوفر أسطولاً متكاملاً من المولدات الجاهزة للإيجار الفوري داخل الأردن بمستويات عزل صوتي ممتازة وكاتمات صوت احترافية. تشمل حلول التأجير كابلات الطاقة، خزان الديزل الخارجي، كادر التشغيل الفني، والصيانة الدورية المجانية طوال فترة العقد.
                </p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-12 border-b border-gray-150 pb-12">
            <div class="md:w-1/3 text-6xl text-center md:text-right">🛠️</div>
            <div class="md:w-2/3 space-y-4">
                <h3 class="text-2xl font-bold text-[#0b192c]">عقود الصيانة الدورية السنوية (SLA)</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    يشتمل عقد الصيانة السنوي على زيارات كشف دوري (كل 3 أشهر أو حسب حاجة العميل) يقوم خلالها فريقنا بفحص فني كامل للمحرك والمولد، وفحص البطاريات، وتغيير الزيت والفلاتر والسيور عند الحاجة لضمان بقاء المولد بكامل طاقته عند انقطاع التيار الرئيسي.
                </p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/3 text-6xl text-center md:text-right">🎛️</div>
            <div class="md:w-2/3 space-y-4">
                <h3 class="text-2xl font-bold text-[#0b192c]">عمرة المولدات وفحص الأحمال (Load Bank)</h3>
                <p class="text-gray-600 text-sm leading-relaxed">
                    نقوم بعمل عمرة كاملة للمحركات وتوفير قطع الغيار النادرة. كما نمتلك أجهزة فحص الأحمال الاصطناعية (Load Bank Testing) لاختبار كفاءة المولدات وتجربة تحميلها بنسب مختلفة (50%, 75%, 100%) للتأكد من قدرتها على تحمل الأحمال الفعلية وحماية المولد من مشكلة (Wet Stacking).
                </p>
            </div>
        </div>

    </div>
</section>
@endsection
