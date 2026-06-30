@extends('layouts.auth')

@section('title', 'استعادة كلمة المرور - CADY EST')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-gray-300 leading-relaxed text-center">
        أدخل البريد الإلكتروني المرتبط بحسابك، وسنقوم بإرسال رابط لإعادة تعيين كلمة مرور جديدة.
    </p>
</div>

<form class="mt-6 space-y-6" action="{{ route('password.email') }}" method="POST">
    @csrf

    <div>
        <label for="email" class="block text-sm font-semibold text-gray-300 mb-1">البريد الإلكتروني المشترك</label>
        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" 
            class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="example@cady-est.com">
    </div>

    @if(session('status'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-200 text-xs p-4 rounded-xl text-center">
            {{ session('status') }}
        </div>
    @endif

    <div>
        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#0b192c] bg-[#00d26a] hover:bg-[#00b058] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00d26a] transition cursor-pointer">
            إرسال رابط استعادة كلمة المرور
        </button>
    </div>

    <div class="text-center text-sm text-gray-300">
        <a href="{{ route('login') }}" class="font-bold text-[#00d26a] hover:underline">العودة لتسجيل الدخول</a>
    </div>
</form>
@endsection
