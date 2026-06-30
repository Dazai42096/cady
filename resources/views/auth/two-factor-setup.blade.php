@extends('layouts.dashboard')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Two-Factor Authentication</h1>
        <p class="text-slate-600 mt-1">Add an extra security layer to your account using Google Authenticator, Authy, or 1Password.</p>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        @if ($user->hasTwoFactorEnabled())
            <div class="mb-5">
                <span class="inline-flex rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-sm font-bold">
                    Enabled
                </span>
            </div>

            <p class="text-slate-700 mb-5">
                Two-factor authentication is currently enabled for your account.
            </p>

            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                <button type="submit" class="rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-3">
                    Disable 2FA
                </button>
            </form>
        @else
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">1. Scan QR Code</h2>

                    <div class="inline-block bg-white border rounded-xl p-4">
                        {!! $qrSvg !!}
                    </div>

                    <p class="text-sm text-slate-600 mt-3">
                        Manual key:
                        <span class="font-mono bg-slate-100 px-2 py-1 rounded">{{ $secret }}</span>
                    </p>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900 mb-3">2. Confirm Code</h2>

                    <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="code" class="block text-sm font-bold text-slate-700 mb-2">
                                6-digit code
                            </label>
                            <input
                                id="code"
                                name="code"
                                type="text"
                                inputmode="numeric"
                                required
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400"
                            >
                        </div>

                        <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3">
                            Enable 2FA
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-lg font-bold text-slate-900 mb-3">Recovery Codes</h2>
                <p class="text-slate-600 mb-3">Save these codes somewhere safe. Each code can be used once if you lose your authenticator app.</p>

                <div class="grid md:grid-cols-2 gap-2">
                    @foreach ($recoveryCodes as $code)
                        <div class="font-mono bg-slate-100 rounded-lg px-3 py-2">{{ $code }}</div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection