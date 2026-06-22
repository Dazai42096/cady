@extends('layouts.auth')

@section('title', 'إنشاء حساب عميل جديد - CADY EST')

@section('content')
<form class="mt-8 space-y-4" action="{{ route('register') }}" method="POST">
    @csrf

    <!-- Company Name -->
    <div>
        <label for="company_name" class="block text-sm font-semibold text-gray-300 mb-1">اسم الشركة</label>
        <input id="company_name" name="company_name" type="text" required value="{{ old('company_name') }}" 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="شركة كادي للتجارة">
    </div>

    <!-- Contact Person -->
    <div>
        <label for="contact_person" class="block text-sm font-semibold text-gray-300 mb-1">الشخص المسؤول (الاسم الثلاثي)</label>
        <input id="contact_person" name="contact_person" type="text" required value="{{ old('contact_person') }}" 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="أحمد علي محمد">
    </div>

    <!-- Phone -->
    <div>
        <label for="phone" class="block text-sm font-semibold text-gray-300 mb-1">رقم الهاتف</label>
        <input id="phone" name="phone" type="text" required value="{{ old('phone') }}" 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="0790000000">
    </div>

    <!-- Email -->
    <div>
        <label for="email" class="block text-sm font-semibold text-gray-300 mb-1">البريد الإلكتروني للشركة</label>
        <input id="email" name="email" type="email" required value="{{ old('email') }}" 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="info@yourcompany.com">
    </div>

    <!-- Address -->
    <div>
        <label for="address" class="block text-sm font-semibold text-gray-300 mb-1">العنوان بالتفصيل</label>
        <input id="address" name="address" type="text" value="{{ old('address') }}" 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="عمان - شارع المدينة المنورة - مجمع رقم 12">
    </div>

    <!-- Business Activity -->
    <div>
        <label for="business_activity" class="block text-sm font-semibold text-gray-300 mb-1">مجال عمل الشركة</label>
        <input id="business_activity" name="business_activity" type="text" value="{{ old('business_activity') }}" 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="صناعي، مستشفى، بنك، إلخ">
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block text-sm font-semibold text-gray-300 mb-1">كلمة المرور</label>
        <input id="password" name="password" type="password" required 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="••••••••">
    </div>

    <!-- Password Confirmation -->
    <div>
        <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-1">تأكيد كلمة المرور</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required 
            class="appearance-none block w-full px-4 py-2.5 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="••••••••">
    </div>

    <!-- Submit -->
    <div class="pt-2">
        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#0b192c] bg-[#00d26a] hover:bg-[#00b058] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00d26a] transition cursor-pointer">
            إنشاء حساب وبدء مراجعة الطلب
        </button>
    </div>

    <!-- Login Link -->
    <div class="text-center text-sm text-gray-300 mt-2">
        لديك حساب بالفعل؟ 
        <a href="{{ route('login') }}" class="font-bold text-[#00d26a] hover:underline">تسجيل الدخول</a>
    </div>
</form>
@endsection
