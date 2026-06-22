@extends('layouts.auth')

@section('title', 'تسجيل الدخول - CADY EST')

@section('content')
<form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
    @csrf

    <div class="space-y-4">
        <!-- Email Input -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-300 mb-1">البريد الإلكتروني</label>
            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" 
                class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
                placeholder="example@cady-est.com">
        </div>

        <!-- Password Input -->
        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-sm font-semibold text-gray-300">كلمة المرور</label>
                <a href="{{ route('password.request') }}" class="text-xs text-[#00d26a] hover:underline">نسيت كلمة المرور؟</a>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required 
                class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
                placeholder="••••••••">
        </div>
    </div>

    <!-- Remember me checkbox -->
    <div class="flex items-center">
        <input id="remember" name="remember" type="checkbox" 
            class="h-4 w-4 text-[#00d26a] focus:ring-[#00d26a] border-white/20 bg-white/5 rounded transition">
        <label for="remember" class="margin-right-2 block text-sm text-gray-300 mr-2 select-none">تذكرني في المرة القادمة</label>
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#0b192c] bg-[#00d26a] hover:bg-[#00b058] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00d26a] transition cursor-pointer">
            تسجيل الدخول
        </button>
    </div>

    <!-- Register link -->
    <div class="text-center text-sm text-gray-300">
        ليس لديك حساب عميل؟ 
        <a href="{{ route('register') }}" class="font-bold text-[#00d26a] hover:underline">سجل شركتك الآن</a>
    </div>
</form>
@endsection
