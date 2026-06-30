@extends('layouts.dashboard')

@section('title', 'User Security')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">User Security</h1>
            <p class="text-slate-600 mt-1">Unlock accounts, reset 2FA, and activate or deactivate users.</p>
        </div>

        <form method="GET" action="{{ route('dashboard.security-users.index') }}" class="flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search name, email, role..."
                class="rounded-xl border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400"
            >
            <button class="rounded-xl bg-slate-900 text-white px-4 py-2 font-bold">
                Search
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
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">Lock</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-600 uppercase">2FA</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-600 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                <div class="text-sm text-slate-500">{{ $user->email }}</div>
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-3 py-1 text-xs font-bold">
                                    {{ ucfirst((string) $user->role) }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                @if ($user->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-bold">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-bold">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if ($user->isLocked())
                                    <div class="text-sm font-bold text-red-700">Locked</div>
                                    <div class="text-xs text-slate-500">{{ $user->locked_until?->format('Y-m-d H:i') }}</div>
                                @else
                                    <span class="text-sm text-slate-500">Not locked</span>
                                @endif

                                <div class="text-xs text-slate-400">
                                    Failed: {{ $user->failed_login_attempts ?? 0 }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                @if ($user->hasTwoFactorEnabled())
                                    <span class="inline-flex rounded-full bg-emerald-100 text-emerald-700 px-3 py-1 text-xs font-bold">
                                        Enabled
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 text-slate-600 px-3 py-1 text-xs font-bold">
                                        Disabled
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <form method="POST" action="{{ route('dashboard.security-users.unlock', $user) }}">
                                        @csrf
                                        <button class="rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3 py-2">
                                            Unlock
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('dashboard.security-users.reset-2fa', $user) }}">
                                        @csrf
                                        <button class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2">
                                            Reset 2FA
                                        </button>
                                    </form>

                                    @if ($user->is_active)
                                        <form method="POST" action="{{ route('dashboard.security-users.deactivate', $user) }}">
                                            @csrf
                                            <button class="rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3 py-2">
                                                Deactivate
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('dashboard.security-users.activate', $user) }}">
                                            @csrf
                                            <button class="rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection