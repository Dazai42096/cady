@extends('layouts.dashboard')

@section('title', 'WhatsApp Message Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">WhatsApp Message</h1>
            <p class="text-slate-600 mt-1">{{ $message->phone }} — {{ ucfirst($message->message_type) }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('dashboard.whatsapp.open', $message) }}">
                @csrf
                <button class="rounded-xl bg-emerald-600 text-white font-bold px-5 py-3">
                    Open WhatsApp
                </button>
            </form>

            @if ($message->status !== 'sent')
                <form method="POST" action="{{ route('dashboard.whatsapp.mark-sent', $message) }}">
                    @csrf
                    <button class="rounded-xl bg-blue-600 text-white font-bold px-5 py-3">
                        Mark Sent
                    </button>
                </form>
            @endif

            <a href="{{ route('dashboard.whatsapp.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">
                Back
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Status</div>
            <div class="text-xl font-bold text-slate-900">{{ ucfirst($message->status) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Type</div>
            <div class="text-xl font-bold text-slate-900">{{ ucfirst($message->message_type) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Created</div>
            <div class="text-xl font-bold text-slate-900">{{ $message->created_at?->format('Y-m-d') }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="text-sm text-slate-500">Sent</div>
            <div class="text-xl font-bold text-slate-900">{{ $message->sent_at?->format('Y-m-d') ?? '-' }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Message Information</h2>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-slate-500">Customer</div>
                <div class="font-bold text-slate-900">{{ $message->customer?->company_name ?? '-' }}</div>
            </div>

            <div>
                <div class="text-slate-500">Phone</div>
                <div class="font-bold text-slate-900 font-mono">{{ $message->phone }}</div>
            </div>

            <div>
                <div class="text-slate-500">Quotation</div>
                <div class="font-bold text-slate-900">{{ $message->quotation?->ref_number ?? '-' }}</div>
            </div>

            <div>
                <div class="text-slate-500">Rental</div>
                <div class="font-bold text-slate-900">{{ $message->rental?->ref_number ?? '-' }}</div>
            </div>

            <div>
                <div class="text-slate-500">Maintenance Contract</div>
                <div class="font-bold text-slate-900">{{ $message->maintenanceContract?->ref_number ?? '-' }}</div>
            </div>

            <div>
                <div class="text-slate-500">Created By</div>
                <div class="font-bold text-slate-900">{{ $message->creator?->name ?? '-' }}</div>
            </div>
        </div>

        <div>
            <div class="text-slate-500 text-sm mb-2">Message Body</div>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 whitespace-pre-wrap text-slate-900">{{ $message->message_body }}</div>
        </div>
    </div>
</div>
@endsection