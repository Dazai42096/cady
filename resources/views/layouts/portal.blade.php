<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'بوابة العملاء - CADY EST')</title>
    
    <!-- Alpine.js CDN for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col font-sans">

    <!-- Top Navigation Header -->
    <header class="bg-[#0b192c] text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            
            <!-- Branding -->
            <a href="{{ route('portal.index') }}" class="flex items-center space-x-2 space-x-reverse">
                <span class="text-xl font-bold tracking-wider text-white">كادي <span class="text-[#00d26a]">بوابة العملاء</span></span>
                <span class="text-xs bg-navy-800 border border-gray-700 text-gray-300 px-2 py-1 rounded">CADY EST</span>
            </a>

            <!-- User Context Action -->
            <div class="flex items-center space-x-4 space-x-reverse">
                
                <!-- Company Name Context Display -->
                <div class="text-left md:text-right">
                    <p class="text-xs text-gray-400">الشركة المتعاقدة</p>
                    <p class="text-sm font-bold text-white">{{ request()->attributes->get('customer')->company_name }}</p>
                </div>

                <div class="border-r border-gray-700 h-8"></div>

                <!-- Contact Person -->
                <span class="text-sm text-gray-200 hidden md:inline">{{ Auth::user()->name }}</span>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-xs font-bold transition cursor-pointer">
                        تسجيل الخروج
                    </button>
                </form>

            </div>

        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-grow py-8 container mx-auto px-4">
        
        <!-- Flash messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-r-4 border-[#00d26a] text-green-800 p-4 rounded-xl flex items-center justify-between" role="alert">
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

    <!-- Footer Support Link -->
    <footer class="bg-[#070f1e] text-gray-500 py-6 border-t border-gray-950 text-center text-xs">
        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
            <p>&copy; 2026 مؤسسة كادي للمولدات والصيانة. جميع الحقوق محفوظة.</p>
            <p>للدعم الفني والاستفسار يرجى الاتصال بـ <span class="text-white">0790000000</span> أو البريد الإلكتروني <span class="text-white">support@cady-est.com</span></p>
        </div>
    </footer>

</body>
</html>
