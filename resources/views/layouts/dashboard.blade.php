<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم - CADY EST')</title>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
        direction: ltr;
        text-align: left;
    }

    html[dir="rtl"] body {
        direction: rtl;
        text-align: right;
    }
</style>
</head>
<body class="bg-gray-100 text-gray-800 antialiased font-sans flex min-h-screen" x-data="{ sidebarOpen: false }">

    <div class="fixed inset-0 z-40 bg-black/50 md:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"></div>

    <aside class="fixed inset-y-0 right-0 z-50 flex flex-col w-64 bg-[#0b192c] text-white transition-transform duration-300 transform md:translate-x-0 md:static md:h-screen" :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
        
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-800 bg-[#070f1e]">
            <a href="/" class="text-xl font-bold tracking-wider text-white">كادي <span class="text-[#00d26a]">للمولدات</span></a>
            <button class="md:hidden text-gray-400 hover:text-white" @click="sidebarOpen = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="flex-grow px-4 py-6 space-y-1 overflow-y-auto">
            
            <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
            </a>

            <hr class="border-gray-800 my-2">

            <a href="{{ route('dashboard.customers.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/customers') || Request::is('dashboard/customers/*') && !Request::is('dashboard/customers-pending') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">👥</span> {{ app()->getLocale() === 'ar' ? 'إدارة العملاء' : 'Customers' }}
            </a>

            @if(Auth::user()->isAdmin())
                <a href="{{ route('dashboard.customers.pending') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/customers-pending') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">⏳</span> {{ app()->getLocale() === 'ar' ? 'عملاء معلقون' : 'Pending Customers' }}
                </a>
            @endif

            <a href="{{ route('dashboard.generators.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/generators') || Request::is('dashboard/generators/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">⚡</span> {{ app()->getLocale() === 'ar' ? 'المولدات الكهربائية' : 'Generators' }}
            </a>

            @if(Auth::user()->isAdmin() || Auth::user()->isSales())
                <a href="{{ route('dashboard.quotations.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/quotations') || Request::is('dashboard/quotations/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">📄</span> {{ app()->getLocale() === 'ar' ? 'عروض الأسعار' : 'Quotations' }}
                </a>
            @endif

            <a href="{{ route('dashboard.contracts.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/contracts') || Request::is('dashboard/contracts/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">✍️</span> {{ app()->getLocale() === 'ar' ? 'عقود الصيانة' : 'Maintenance Contracts' }}
            </a>

            <a href="{{ route('dashboard.visits.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/visits') || Request::is('dashboard/visits/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">🔧</span> {{ app()->getLocale() === 'ar' ? 'زيارات الخدمة الميدانية' : 'Field Service Visits' }}
            </a>

            <hr class="border-gray-800 my-2">

            @if(Auth::user()->isAdmin() || Auth::user()->isSales())
                <a href="{{ route('dashboard.quote_requests.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/quote-requests') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">📬</span> {{ app()->getLocale() === 'ar' ? 'طلبات الموقع العام' : 'Website Requests' }}
                </a>
            @endif

            @if(Auth::user()->isAdmin())
                <a href="{{ route('dashboard.audit_logs.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/audit-logs') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">🕵️‍♂️</span> {{ app()->getLocale() === 'ar' ? 'سجل العمليات' : 'Audit Log' }} (Audit)
                </a>
            @endif

        
                <a href="{{ route('dashboard.backups.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard.backups.*') ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span>💾</span>
            @if(auth()->user()?->role === 'admin')
                <a href="/dashboard/employees"
                   style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;color:#e5edf7;text-decoration:none;font-weight:700;">
                    <span>👥</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'إدارة الموظفين' : 'Employees' }}</span>
                </a>
            @endif

            <a href="/dashboard/rentals"
               style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;color:#e5edf7;text-decoration:none;font-weight:700;">
                <span>🚚</span>
                <span>{{ app()->getLocale() === 'ar' ? 'التحكم بالتأجير' : 'Rental Control' }}</span>
            </a>

            <a href="/dashboard/service-reports"
               style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;color:#e5edf7;text-decoration:none;font-weight:700;">
                <span>🛠️</span>
                <span>{{ app()->getLocale() === 'ar' ? 'تقارير الخدمة' : 'Service Reports' }}</span>
            </a>

            <a href="/dashboard/whatsapp-messages"
               style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:12px;color:#e5edf7;text-decoration:none;font-weight:700;">
                <span>💬</span>
                <span>{{ app()->getLocale() === 'ar' ? 'رسائل واتساب' : 'WhatsApp Messages' }}</span>
            </a>
Backups</span>
                </a>
            
                <a href="{{ route('dashboard.compliance.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard.compliance.*') ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span>✅</span>
                    <span>Compliance</span>
                </a>
            </nav>

        <div class="p-4 border-t border-gray-800 bg-[#070f1e] text-center text-xs text-gray-500">
            {{ app()->getLocale() === 'ar' ? 'كادي للمولدات' : 'CADY Generators' }} والصيانة &copy; 2026
        </div>
    </aside>

    <div class="flex-grow flex flex-col min-h-screen overflow-x-hidden">
        
        <header class="bg-white border-b border-gray-200 py-4 px-6 flex justify-between items-center z-10">
            
            <button class="md:hidden text-gray-600 hover:text-black focus:outline-none" @click="sidebarOpen = true">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <h1 class="text-xl font-bold text-[#0b192c] hidden md:block">@yield('page_title', 'لوحة إدارة المنظومة')</h1>

            <div class="flex items-center space-x-4 space-x-reverse">
                
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#0b192c] text-white">
                    {{ ucfirst((string) Auth::user()->role) }}
                </span>

                <div class="flex items-center space-x-2 space-x-reverse">
                    <span class="text-sm font-semibold hidden sm:inline text-gray-700">{{ Auth::user()->name }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'تسجيل خروج' : 'Logout' }}
                    </button>
                </form>

            </div>
        </header>

        <main class="flex-grow p-6">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-r-4 border-green-500 text-green-800 p-4 rounded-xl flex items-center justify-between" role="alert">
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button class="font-bold text-green-950 text-lg hover:text-black cursor-pointer" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border-r-4 border-red-500 text-red-800 p-4 rounded-xl flex items-center justify-between" role="alert">
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button class="font-bold text-red-950 text-lg hover:text-black cursor-pointer" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
<script src="{{ asset('js/cady-i18n.js') }}?v=20260701"></script>
</body>
</html>
