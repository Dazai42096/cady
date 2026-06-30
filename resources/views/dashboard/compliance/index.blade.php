@extends('layouts.dashboard')

@section('title', 'Compliance Dashboard')

@section('content')
<style>
@media print {
    body {
        background: white !important;
    }

    aside,
    nav,
    header,
    .no-print {
        display: none !important;
    }

    main,
    .content,
    .container {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    a {
        text-decoration: none !important;
        color: #000 !important;
    }

    .shadow-sm,
    .shadow,
    .shadow-lg {
        box-shadow: none !important;
    }

    .rounded-2xl,
    .rounded-xl,
    .rounded-3xl {
        border-radius: 8px !important;
    }
}
</style>
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Master Spec Compliance Dashboard</h1>
            <p class="text-slate-600 mt-1">A quick internal checklist for testing the CADY system modules.</p>
        </div>

        <div class="flex gap-2 no-print">
    <button onclick="window.print()" class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">
        Print / Save PDF
    </button>

    <a href="{{ route('dashboard.index') }}" class="rounded-xl bg-slate-900 text-white font-bold px-5 py-3">
        Back to Dashboard
    </a>
</div>
    </div>

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Total Modules</div>
            <div class="text-3xl font-black text-slate-900">{{ $summary['total'] }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Complete</div>
            <div class="text-3xl font-black text-emerald-600">{{ $summary['complete'] }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Partial</div>
            <div class="text-3xl font-black text-amber-600">{{ $summary['partial'] }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="text-sm text-slate-500">Missing</div>
            <div class="text-3xl font-black text-red-600">{{ $summary['missing'] }}</div>
        </div>
    </div>

    <div class="space-y-4">
        @foreach ($modules as $module)
            @php
                $allPassed = collect($module['checks'])->every(fn ($value) => $value === true);
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-bold text-slate-900">{{ $module['name'] }}</h2>

                            @if ($allPassed)
                                <span class="inline-flex rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-bold">
                                    PASS
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-bold">
                                    CHECK
                                </span>
                            @endif
                        </div>

                        <p class="text-slate-600 mt-1">{{ $module['description'] }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach ($module['links'] as $label => $url)
                            @if ($url)
                                <a href="{{ $url }}" class="rounded-lg bg-slate-900 text-white text-xs font-bold px-3 py-2">
                                    {{ $label }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 grid md:grid-cols-2 gap-2">
                    @foreach ($module['checks'] as $check => $passed)
                        <div class="flex items-center justify-between bg-slate-50 rounded-xl px-4 py-3">
                            <span class="text-sm text-slate-700">{{ $check }}</span>

                            @if ($passed)
                                <span class="text-emerald-600 font-bold text-sm">OK</span>
                            @else
                                <span class="text-red-600 font-bold text-sm">Missing</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-900 text-sm">
        <strong>Testing note:</strong> This page checks route/table existence. It does not replace manual testing of create, update, export, login, and role-permission flows.
    </div>
</div>
@endsection