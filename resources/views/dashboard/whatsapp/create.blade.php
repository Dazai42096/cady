@extends('layouts.dashboard')

@section('title', 'Create WhatsApp Message')

@section('content')
<div class="max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Create WhatsApp Message</h1>
        <p class="text-slate-600 mt-1">Create a tracked WhatsApp message and open it in WhatsApp Web.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('dashboard.whatsapp.store') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
        @csrf

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Customer</label>
                <select name="customer_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">No customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id') === $customer->id)>
                            {{ $customer->company_name }} — {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Phone</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    placeholder="0790000000 or 962790000000"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3"
                >
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Message Type</label>
                <select name="message_type" required class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    @foreach (['general', 'quotation', 'rental', 'maintenance', 'payment', 'support'] as $type)
                        <option value="{{ $type }}" @selected(old('message_type', 'general') === $type)>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Related Quotation</label>
                <select name="quotation_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">None</option>
                    @foreach ($quotations as $quotation)
                        <option value="{{ $quotation->id }}" @selected(old('quotation_id') === $quotation->id)>
                            {{ $quotation->ref_number }} — {{ $quotation->customer?->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Related Rental</label>
                <select name="rental_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">None</option>
                    @foreach ($rentals as $rental)
                        <option value="{{ $rental->id }}" @selected(old('rental_id') === $rental->id)>
                            {{ $rental->ref_number }} — {{ $rental->customer?->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Related Maintenance Contract</label>
                <select name="maintenance_contract_id" class="w-full rounded-xl border border-slate-300 px-4 py-3">
                    <option value="">None</option>
                    @foreach ($contracts as $contract)
                        <option value="{{ $contract->id }}" @selected(old('maintenance_contract_id') === $contract->id)>
                            {{ $contract->ref_number }} — {{ $contract->customer?->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Message Body</label>
            <textarea name="message_body" rows="7" required class="w-full rounded-xl border border-slate-300 px-4 py-3" placeholder="Write the WhatsApp message here...">{{ old('message_body') }}</textarea>
        </div>

        <div class="flex gap-3">
            <button class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3">
                Create Message
            </button>

            <a href="{{ route('dashboard.whatsapp.index') }}" class="rounded-xl bg-slate-100 text-slate-700 font-bold px-5 py-3">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection