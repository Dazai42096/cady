@extends('layouts.public')

@section('title', 'من نحن - كادي للمولدات والصيانة - CADY EST')

@section('content')
<!-- Page Header Banner -->
<section class="bg-[#0b192c] text-white py-16 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold">من نحن</h1>
    <p class="text-gray-400 mt-3 text-sm md:text-base">تاريخ ومستقبل مؤسسة كادي للمولدات وحلول الطاقة</p>
</section>

<!-- Company Info -->
<section class="py-20 bg-white">
    <div class="container mx-auto max-w-4xl px-4 flex flex-col md:flex-row items-center gap-12">
        
        <div class="md:w-1/2 space-y-6">
            <h2 class="text-2xl font-bold text-[#0b192c] border-r-4 border-[#00d26a] pr-4">رواد في توفير الطاقة المستدامة منذ سنوات</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                تأسست مؤسسة كادي للمولدات الكهربائية والصيانة في الأردن بهدف سد الفجوة في قطاع المولدات وتقديم خدمات صيانة فنية متميزة ومحترفة. منذ انطلاقنا، نجحنا في خدمة المئات من الشركات والمستشفيات والمصانع الكبرى.
            </p>
            <p class="text-gray-600 text-sm leading-relaxed">
                نحن لا نوفر مجرد مولدات كهربائية، بل نوفر حلول طاقة ذكية ومستمرة وراحة بال كاملة لعملائنا من خلال شبكة واسعة من المهندسين والفنيين المؤهلين للتعامل مع مختلف الظروف الفنية والطارئة على مدار الساعة.
            </p>
        </div>

        <div class="md:w-1/2 bg-gray-50 border border-gray-200 p-8 rounded-2xl shadow-sm text-center">
            <span class="text-5xl">🎯</span>
            <h3 class="text-xl font-bold text-[#0b192c] mt-4">رسالتنا</h3>
            <p class="text-gray-500 text-sm mt-3 leading-relaxed">
                تقديم مولدات كهربائية وخدمات صيانة بأعلى معايير الجودة العالمية، والمساهمة في دعم الاقتصاد المحلي عن طريق توفير طاقة بديلة وموثوقة خالية من الانقطاعات وبكفاءة تشغيلية ممتازة.
            </p>
        </div>

    </div>
</section>

<!-- Mission & Vision -->
<section class="py-20 bg-gray-50 border-t border-b border-gray-200">
    <div class="container mx-auto max-w-4xl px-4 grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-3">
            <span class="text-3xl">👁️</span>
            <h3 class="text-xl font-bold text-[#0b192c]">رؤيتنا</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                أن نكون الخيار الأول والوجهة الأكثر موثوقية في الأردن والشرق الأوسط لتوريد وتأجير وصيانة المولدات الكهربائية الثقيلة والخفيفة بحلول مبتكرة.
            </p>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm space-y-3">
            <span class="text-3xl">🤝</span>
            <h3 class="text-xl font-bold text-[#0b192c]">قيمنا الأساسية</h3>
            <ul class="text-gray-500 text-sm space-y-2">
                <li>• <strong>الموثوقية:</strong> نلتزم بمواعيد الزيارات وعقود الصيانة بدقة متناهية.</li>
                <li>• <strong>الجودة:</strong> استخدام قطع الغيار الأصلية والفلاتر المطابقة للمواصفات.</li>
                <li>• <strong>الأمان:</strong> معايير سلامة وحماية عالية لجميع المنشآت التشغيلية.</li>
                <li>• <strong>السرعة:</strong> الاستجابة الفورية والقصوى للأعطال الفنية والطارئة.</li>
            </ul>
        </div>

    </div>
</section>
@endsection
