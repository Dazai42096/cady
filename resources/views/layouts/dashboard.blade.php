<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم - CADY EST')</title>
    
    <!-- Alpine.js CDN for mobile menu and dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 antialiased font-sans flex min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div class="fixed inset-0 z-40 bg-black/50 md:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;"></div>

    <!-- Sidebar Container -->
    <aside class="fixed inset-y-0 right-0 z-50 flex flex-col w-64 bg-[#0b192c] text-white transition-transform duration-300 transform md:translate-x-0 md:static md:h-screen" :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
        
        <!-- Sidebar Brand Logo -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-800 bg-[#070f1e]">
            <a href="/" class="text-xl font-bold tracking-wider text-white">كادي <span class="text-[#00d26a]">للمولدات</span></a>
            <button class="md:hidden text-gray-400 hover:text-white" @click="sidebarOpen = false">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-grow px-4 py-6 space-y-1 overflow-y-auto">
            
            <a href="{{ route('dashboard.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">📊</span> لوحة الإحصائيات
            </a>

            <hr class="border-gray-800 my-2">

            <!-- Customers -->
            <a href="{{ route('dashboard.customers.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/customers') || Request::is('dashboard/customers/*') && !Request::is('dashboard/customers-pending') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">👥</span> إدارة العملاء
            </a>

            <!-- Pending Customers (Admin only) -->
            @if(Auth::user()->isAdmin())
                <a href="{{ route('dashboard.customers.pending') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/customers-pending') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">⏳</span> عملاء معلقون
                </a>
            @endif

            <!-- Generators -->
            <a href="{{ route('dashboard.generators.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/generators') || Request::is('dashboard/generators/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">⚡</span> المولدات الكهربائية
            </a>

            <!-- Quotations (Admin/Sales only) -->
            @if(Auth::user()->isAdmin() || Auth::user()->isSales())
                <a href="{{ route('dashboard.quotations.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/quotations') || Request::is('dashboard/quotations/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">📄</span> عروض الأسعار
                </a>
            @endif

            <!-- Maintenance Contracts -->
            <a href="{{ route('dashboard.contracts.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/contracts') || Request::is('dashboard/contracts/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">✍️</span> عقود الصيانة
            </a>

            <!-- Maintenance Visits -->
            <a href="{{ route('dashboard.visits.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/visits') || Request::is('dashboard/visits/*') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                <span class="ml-3">🔧</span> زيارات الخدمة الميدانية
            </a>

            <hr class="border-gray-800 my-2">

            <!-- Public Quote Requests (Admin/Sales only) -->
            @if(Auth::user()->isAdmin() || Auth::user()->isSales())
                <a href="{{ route('dashboard.quote_requests.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/quote-requests') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">📬</span> طلبات الموقع العام
                </a>
            @endif

            <!-- Audit Logs (Admin only) -->
            @if(Auth::user()->isAdmin())
                <a href="{{ route('dashboard.audit_logs.index') }}" class="flex items-center px-4 py-3 rounded-lg hover:bg-navy-800 transition {{ Request::is('dashboard/audit-logs') ? 'sidebar-active font-semibold' : 'text-gray-300' }}">
                    <span class="ml-3">🕵️‍♂️</span> سجل العمليات (Audit)
                </a>
            @endif

        
                <a href="{{ route('dashboard.backups.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard.backups.*') ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span>💾</span>
                    <span>Backups</span>
                </a>
            
                <a href="{{ route('dashboard.compliance.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard.compliance.*') ? 'bg-emerald-500 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span>✅</span>
                    <span>Compliance</span>
                </a>
            </nav>

        <!-- Sidebar Footer Info -->
        <div class="p-4 border-t border-gray-800 bg-[#070f1e] text-center text-xs text-gray-500">
            كادي للمولدات والصيانة &copy; 2026
        </div>
    </aside>

    <!-- Main Workspace Container -->
    <div class="flex-grow flex flex-col min-h-screen overflow-x-hidden">
        
        <!-- Top Workspace Header -->
        <header class="bg-white border-b border-gray-200 py-4 px-6 flex justify-between items-center z-10">
            
            <!-- Mobile Sidebar Toggle -->
            <button class="md:hidden text-gray-600 hover:text-black focus:outline-none" @click="sidebarOpen = true">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Page Title Header (dynamic/fallback) -->
            <h1 class="text-xl font-bold text-[#0b192c] hidden md:block">@yield('page_title', 'لوحة إدارة المنظومة')</h1>

            <!-- User Context Action Menu -->
            <div class="flex items-center space-x-4 space-x-reverse">
                
                <!-- Role Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#0b192c] text-white">
                    {{ ucfirst((string) Auth::user()->role) }}
                </span>

                <!-- User Profile Dropdown / Static display -->
                <div class="flex items-center space-x-2 space-x-reverse">
                    <span class="text-sm font-semibold hidden sm:inline text-gray-700">{{ Auth::user()->name }}</span>
                </div>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                        تسجيل خروج
                    </button>
                </form>

            </div>
        </header>

        <!-- Dynamic Content Body -->
        <main class="flex-grow p-6">
            
            <!-- Success/Alert Notifications -->
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

</body>
</html>
