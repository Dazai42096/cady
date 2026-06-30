@extends('layouts.auth')

@section('title', 'Two-Factor Authentication')

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-white">Two-Factor Authentication</h1>
        <p class="text-slate-300 mt-2">Enter the code from your authenticator app.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-500/20 border border-red-500 text-red-100 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-bold text-slate-200 mb-2">Authentication Code</label>
            <input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                required
                autofocus
                class="w-full rounded-xl bg-slate-800 border border-slate-600 text-white px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-400"
            >
        </div>

        <button type="submit" class="w-full rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-3">
            Verify
        </button>
    </form>
@endsection