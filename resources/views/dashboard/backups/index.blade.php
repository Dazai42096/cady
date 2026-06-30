@extends('layouts.dashboard')

@section('title', 'Backups')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">System Backups</h1>
            <p class="text-slate-600 mt-1">Generate and download JSON backups of the main CADY business data.</p>
        </div>

        <form method="POST" action="{{ route('dashboard.backups.generate') }}">
            @csrf
            <button style="background:#059669;color:white;padding:12px 20px;border-radius:12px;font-weight:700;text-decoration:none;display:inline-block;">
                Generate Backup
            </button>
        </form>
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

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">File</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Size</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Modified</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($files as $file)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-mono font-bold text-slate-900">{{ $file['name'] }}</div>
                            </td>

                            <td class="px-4 py-4">
                                {{ $file['size'] }}
                            </td>

                            <td class="px-4 py-4">
                                {{ $file['modified_at'] }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('dashboard.backups.download', $file['name']) }}" class="rounded-lg bg-blue-600 text-white text-xs font-bold px-3 py-2">
                                        Download
                                    </a>

                                    <form method="POST" action="{{ route('dashboard.backups.delete', $file['name']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-red-600 text-white text-xs font-bold px-3 py-2">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                                No backup files found. Click Generate Backup to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-900 text-sm">
        <strong>Note:</strong> This creates an application-level JSON export. For production disaster recovery, keep Render/Neon database backups enabled as well.
    </div>
</div>
@endsection