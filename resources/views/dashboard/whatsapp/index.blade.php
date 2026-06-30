@extends('layouts.dashboard')

@section('title', 'WhatsApp Messages')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">WhatsApp Messages</h1>
            <p class="text-slate-600 mt-1">Create, open, and track WhatsApp customer communication records.</p>
        </div>

        <a href="{{ route('dashboard.whatsapp.create') }}" style="background:#059669;color:white;padding:12px 20px;border-radius:12px;font-weight:700;text-decoration:none;display:inline-block;">
            New WhatsApp Message
        </a>
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

    <form method="GET" action="{{ route('dashboard.whatsapp.index') }}" class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col md:flex-row gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search phone, customer, message..."
            class="flex-1 rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400"
        >

        <select name="status" class="rounded-xl border border-slate-300 px-4 py-2">
            <option value="">All statuses</option>
            @foreach (['draft', 'opened', 'sent'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <select name="message_type" class="rounded-xl border border-slate-300 px-4 py-2">
            <option value="">All types</option>
            @foreach (['general', 'quotation', 'rental', 'maintenance', 'payment', 'support'] as $type)
                <option value="{{ $type }}" @selected(request('message_type') === $type)>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>

        <button class="rounded-xl bg-slate-900 text-white font-bold px-5 py-2">
            Filter
        </button>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Created By</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($messages as $message)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-900">{{ $message->customer?->company_name ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $message->created_at?->format('Y-m-d H:i') }}</div>
                            </td>

                            <td class="px-4 py-4 font-mono">
                                {{ $message->phone }}
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-bold">
                                    {{ ucfirst($message->message_type) }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold
                                    @class([
                                        'bg-slate-100 text-slate-700' => $message->status === 'draft',
                                        'bg-blue-100 text-blue-700' => $message->status === 'opened',
                                        'bg-emerald-100 text-emerald-700' => $message->status === 'sent',
                                    ])">
                                    {{ ucfirst($message->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                {{ $message->creator?->name ?? '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('dashboard.whatsapp.show', $message) }}" class="rounded-lg bg-slate-900 text-white text-xs font-bold px-3 py-2">
                                        View
                                    </a>

                                    <form method="POST" action="{{ route('dashboard.whatsapp.open', $message) }}">
                                        @csrf
                                        <button class="rounded-lg bg-emerald-600 text-white text-xs font-bold px-3 py-2">
                                            Open WhatsApp
                                        </button>
                                    </form>

                                    @if ($message->status !== 'sent')
                                        <form method="POST" action="{{ route('dashboard.whatsapp.mark-sent', $message) }}">
                                            @csrf
                                            <button class="rounded-lg bg-blue-600 text-white text-xs font-bold px-3 py-2">
                                                Mark Sent
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                No WhatsApp messages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection