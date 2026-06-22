<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'كادي للمولدات والصيانة - CADY EST')</title>
    
    <!-- Alpine.js CDN for interactions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Header Navbar -->
    <header class="bg-[#0b192c] text-white shadow-md sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2 space-x-reverse">
                <span class="text-2xl font-bold tracking-wider text-white">كادي <span class="text-[#00d26a]">للمولدات</span></span>
                <span class="text-xs bg-navy-800 border border-gray-700 text-gray-300 px-2 py-1 rounded">CADY EST</span>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center space-x-8 space-x-reverse">
                <a href="{{ route('home') }}" class="hover:text-[#00d26a] transition {{ Request::is('/') ? 'text-[#00d26a]' : '' }}">الرئيسية</a>
                <a href="{{ route('about') }}" class="hover:text-[#00d26a] transition {{ Request::is('about') ? 'text-[#00d26a]' : '' }}">من نحن</a>
                <a href="{{ route('services') }}" class="hover:text-[#00d26a] transition {{ Request::is('services') ? 'text-[#00d26a]' : '' }}">خدماتنا</a>
                <a href="{{ route('quote_request.form') }}" class="hover:text-[#00d26a] transition {{ Request::is('quote-request') ? 'text-[#00d26a]' : '' }}">طلب عرض سعر</a>
                <a href="{{ route('contact') }}" class="hover:text-[#00d26a] transition {{ Request::is('contact') ? 'text-[#00d26a]' : '' }}">اتصل بنا</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center space-x-4 space-x-reverse">
                @auth
                    @if(Auth::user()->isStaff())
                        <a href="{{ route('dashboard.index') }}" class="bg-[#00d26a] hover:bg-[#00b058] text-[#0b192c] font-bold px-5 py-2 rounded-lg transition">لوحة التحكم</a>
                    @else
                        <a href="{{ route('portal.index') }}" class="bg-[#00d26a] hover:bg-[#00b058] text-[#0b192c] font-bold px-5 py-2 rounded-lg transition">بوابة العملاء</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-300 hover:text-white transition cursor-pointer">تسجيل الخروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="bg-[#00d26a] hover:bg-[#00b058] text-[#0b192c] font-bold px-5 py-2 rounded-lg transition">حساب جديد</a>
                @endauth
            </div>

            <!-- Mobile Hamburger Menu Button -->
            <button class="md:hidden text-white hover:text-[#00d26a] focus:outline-none" @click="mobileMenuOpen = !mobileMenuOpen">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden bg-[#070f1e] border-t border-gray-800" x-show="mobileMenuOpen" x-transition>
            <div class="px-2 pt-2 pb-4 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md hover:bg-navy-800 {{ Request::is('/') ? 'text-[#00d26a]' : '' }}">الرئيسية</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md hover:bg-navy-800 {{ Request::is('about') ? 'text-[#00d26a]' : '' }}">من نحن</a>
                <a href="{{ route('services') }}" class="block px-3 py-2 rounded-md hover:bg-navy-800 {{ Request::is('services') ? 'text-[#00d26a]' : '' }}">خدماتنا</a>
                <a href="{{ route('quote_request.form') }}" class="block px-3 py-2 rounded-md hover:bg-navy-800 {{ Request::is('quote-request') ? 'text-[#00d26a]' : '' }}">طلب عرض سعر</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md hover:bg-navy-800 {{ Request::is('contact') ? 'text-[#00d26a]' : '' }}">اتصل بنا</a>
                
                <hr class="border-gray-800 my-2">
                @auth
                    @if(Auth::user()->isStaff())
                        <a href="{{ route('dashboard.index') }}" class="block px-3 py-2 text-center bg-[#00d26a] text-[#0b192c] font-bold rounded-md">لوحة التحكم</a>
                    @else
                        <a href="{{ route('portal.index') }}" class="block px-3 py-2 text-center bg-[#00d26a] text-[#0b192c] font-bold rounded-md">بوابة العملاء</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="block w-full px-3 py-2">
                        @csrf
                        <button type="submit" class="w-full text-center text-red-400 hover:text-red-500 font-bold">تسجيل الخروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center px-3 py-2 text-gray-300 hover:text-white">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="block text-center px-3 py-2 bg-[#00d26a] text-[#0b192c] font-bold rounded-md">حساب جديد</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @if(session('success'))
            <div class="container mx-auto px-4 mt-6">
                <div class="bg-green-100 border-r-4 border-[#00d26a] text-green-800 p-4 rounded-lg flex items-center justify-between" role="alert">
                    <span>{{ session('success') }}</span>
                    <button class="font-bold text-green-900 cursor-pointer" onclick="this.parentElement.remove()">×</button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#070f1e] text-gray-400 pt-12 pb-6 border-t border-gray-900">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Company Intro -->
            <div>
                <span class="text-xl font-bold tracking-wider text-white">مؤسسة كادي <span class="text-[#00d26a]">للمولدات</span></span>
                <p class="mt-4 text-sm leading-relaxed">
                    نحن متخصصون في توريد، تأجير، وصيانة المولدات الكهربائية لكافة القطاعات الصناعية والتجارية والصحية في المملكة الأردنية الهاشمية.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-bold text-lg mb-4">روابط سريعة</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">الرئيسية</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">من نحن</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white transition">خدماتنا</a></li>
                    <li><a href="{{ route('quote_request.form') }}" class="hover:text-white transition">طلب عرض سعر</a></li>
                </ul>
            </div>

            <!-- Contacts Info -->
            <div>
                <h3 class="text-white font-bold text-lg mb-4">معلومات الاتصال</h3>
                <ul class="space-y-2 text-sm">
                    <li class="flex items-center space-x-2 space-x-reverse">
                        <span>📞</span>
                        <span>0790000000 / 065000000</span>
                    </li>
                    <li class="flex items-center space-x-2 space-x-reverse">
                        <span>✉️</span>
                        <span>info@cady-est.com</span>
                    </li>
                    <li class="flex items-center space-x-2 space-x-reverse">
                        <span>📍</span>
                        <span>عمان، الأردن</span>
                    </li>
                    <li class="flex items-center space-x-2 space-x-reverse pt-2">
                        <a href="https://wa.me/962790000000" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold transition flex items-center space-x-2 space-x-reverse">
                            <span>🟢</span>
                            <span>تواصل عبر واتساب</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <hr class="border-gray-800 my-8">

        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-between items-center text-xs">
            <p>&copy; 2026 مؤسسة كادي للمولدات والصيانة (CADY EST). جميع الحقوق محفوظة.</p>
            <p class="mt-2 md:mt-0">تصميم وتطوير احترافي بنظام كادي</p>
        </div>
    </footer>

</body>
</html>
