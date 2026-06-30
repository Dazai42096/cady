<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'تسجيل الدخول - CADY EST')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-tr from-[#070f1e] via-[#0b192c] to-[#1e3e62] min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased text-gray-200">

    <div class="max-w-md w-full space-y-8 bg-white/10 backdrop-blur-md border border-white/10 p-8 rounded-2xl shadow-2xl text-white">
        
        <div class="text-center">
            <a href="/" class="text-3xl font-extrabold tracking-wider">كادي <span class="text-[#00d26a]">للمولدات</span></a>
            <p class="mt-2 text-sm text-gray-300">منصة إدارة الصيانة وتأجير المولدات</p>
        </div>

        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/50 text-green-200 text-sm p-4 rounded-xl text-center">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 text-sm p-4 rounded-xl space-y-1">
                @foreach($errors->all() as $error)
                    <p class="text-center">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @yield('content')

    </div>

<script src="{{ asset('js/cady-i18n.js') }}?v=20260701"></script>
</body>
</html>
