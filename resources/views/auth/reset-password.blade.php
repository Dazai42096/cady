@extends('layouts.auth')

@section('title', 'تعيين كلمة المرور الجديدة - CADY EST')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-gray-300 leading-relaxed text-center">
        يرجى إدخال كلمة المرور الجديدة وتأكيدها لحسابك: 
        <span class="font-bold text-white block mt-1">{{ $email }}</span>
    </p>
</div>

<form class="mt-6 space-y-4" action="{{ route('password.update') }}" method="POST">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <div>
        <label for="password" class="block text-sm font-semibold text-gray-300 mb-1">كلمة المرور الجديدة</label>
        <input id="password" name="password" type="password" required autocomplete="new-password"
            class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="••••••••">
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-1">تأكيد كلمة المرور الجديدة</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
            class="appearance-none block w-full px-4 py-3 border border-white/20 rounded-xl bg-white/5 placeholder-gray-400 text-white focus:outline-none focus:ring-2 focus:ring-[#00d26a] focus:border-transparent transition text-sm" 
            placeholder="••••••••">
    </div>

    <div class="pt-2">
        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-[#0b192c] bg-[#00d26a] hover:bg-[#00b058] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00d26a] transition cursor-pointer">
            تحديث كلمة المرور والدخول
        </button>
    </div>
</form>
@endsection
