<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin BPS - RINGKAS</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        .heading-title {
            font-family: 'Outfit', sans-serif;
        }
        /* Soft animation for alert and hover */
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="relative min-h-screen flex items-center justify-center p-4 selection:bg-bps-lightBlue selection:text-white">

    <!-- BACKGROUND DECORATIONS -->
    
    <!-- Top-Left Blur Aksen Biru -->
    <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-blue-400 opacity-20 blur-3xl pointer-events-none"></div>
    <div class="absolute top-0 left-0 w-80 h-80 bg-gradient-to-br from-blue-100 to-transparent opacity-40 rounded-full blur-2xl pointer-events-none"></div>

    <!-- Top-Right Dot Grid -->
    <svg class="absolute top-10 right-10 text-slate-200 pointer-events-none" width="100" height="100" fill="currentColor" viewBox="0 0 100 100">
        <defs>
            <pattern id="dots" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                <circle cx="3" cy="3" r="3" />
            </pattern>
        </defs>
        <rect width="100" height="100" fill="url(#dots)" />
    </svg>

    <!-- Bottom-Left Dot Grid -->
    <svg class="absolute bottom-10 left-10 text-slate-200 pointer-events-none" width="100" height="100" fill="currentColor" viewBox="0 0 100 100">
        <rect width="100" height="100" fill="url(#dots)" />
    </svg>

    <!-- Bottom-Right Wave Graphics (Matching the colors in the mockup) -->
    <div class="absolute bottom-0 right-0 w-full md:w-1/2 h-64 pointer-events-none z-0">
        <svg class="w-full h-full" viewBox="0 0 500 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Ocean Blue Wave -->
            <path d="M150,200 C300,160 400,20 500,80 L500,200 Z" fill="#0266b3" opacity="0.6"/>
            <!-- Bright Cyan Wave -->
            <path d="M250,200 C350,140 420,50 500,100 L500,200 Z" fill="#00d2ff" opacity="0.4"/>
            <!-- Teal/Green Wave -->
            <path d="M50,200 C200,180 350,80 500,130 L500,200 Z" fill="#43b02a" opacity="0.3"/>
            <!-- Deep Navy Accent Wave -->
            <path d="M320,200 C400,160 450,120 500,145 L500,200 Z" fill="#0f2a4a" opacity="0.5"/>
        </svg>
    </div>

    <!-- MAIN CARD -->
    <div class="relative z-10 w-full max-w-[460px] bg-white rounded-[32px] shadow-[0_10px_40px_rgba(15,42,74,0.08)] border border-slate-100 p-8 md:p-10 transition-all duration-300 hover:shadow-[0_15px_50px_rgba(15,42,74,0.12)]">
        
        <!-- BPS Logo & Header Section -->
        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-24 h-16 mb-4 flex items-center justify-center">
                <!-- Using the downloaded public/images/logo-bps.svg -->
                <img src="{{ asset('images/logo-bps.svg') }}" alt="Logo BPS" class="w-full h-full object-contain">
            </div>
            
            <span class="heading-font text-2xl font-extrabold text-bps-navy tracking-tight leading-tight block">
                RINGKAS
            </span>
            <h1 class="heading-font text-base font-extrabold text-slate-700 mt-1">
                Login Admin BPS
            </h1>
            <p class="text-xs text-slate-400 mt-1 font-medium">
                Sistem Ringkasan Publikasi Statistik BPS
            </p>
        </div>

        <!-- Alert Error Message -->
        @if (session('error'))
            <div class="fade-in mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-2xl text-sm" role="alert">
                <!-- X Circle Icon -->
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-semibold">Masuk Gagal:</span> {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- Validation Error Message (e.g. required fields empty) -->
        @if ($errors->any())
            <div class="fade-in mb-6 flex flex-col gap-1 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-sm">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0"></span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- LOGIN FORM -->
        <form action="{{ route('admin.login.post') }}" method="POST" autocomplete="off" class="space-y-5">
            @csrf
            
            <!-- Username Field -->
            <div class="space-y-1">
                <label for="username" class="sr-only">Username</label>
                <div class="relative group">
                    <!-- Icon User -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-bps-lightBlue transition-colors duration-200">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        placeholder="Username" 
                        value="{{ old('username') }}" 
                        required
                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100 transition-all duration-200 font-medium text-[15px]"
                    >
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-1">
                <label for="password" class="sr-only">Password</label>
                <div class="relative group">
                    <!-- Icon Lock -->
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-bps-lightBlue transition-colors duration-200">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <circle cx="7" cy="12" r="5" />
                            <circle cx="7" cy="12" r="1.8" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12h10m-3 0v4h-3.5v-4" />
                        </svg>
                    </span>
                    
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Password" 
                        required
                        class="w-full pl-11 pr-12 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-bps-lightBlue focus:ring-4 focus:ring-blue-100 transition-all duration-200 font-medium text-[15px]"
                    >

                    <!-- Toggle Visibility Eye Button -->
                    <button 
                        type="button" 
                        id="toggle-password" 
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-600 transition-colors duration-200 focus:outline-none"
                        title="Tampilkan Password"
                    >
                        <!-- Eye Icon (Visible when type is password) -->
                        <svg id="eye-icon-open" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <!-- Eye Off Icon (Visible when type is text) -->
                        <svg id="eye-icon-closed" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Login Button -->
            <button 
                type="submit" 
                class="w-full flex items-center justify-center gap-2 py-3.5 bg-bps-lightBlue hover:bg-blue-700 text-white rounded-2xl font-semibold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 active:shadow-md transition-all duration-200 text-[15px]"
            >
                <!-- Sign In Icon -->
                <svg class="w-5 h-5 stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 8l4 4m0 0l-4 4m4-4H3m5 4v1a3 3 0 003 3h7a3 3 0 003-3V7a3 3 0 00-3-3h-7a3 3 0 00-3 3v1" />
                </svg>
                <span>Masuk</span>
            </button>
        </form>

        <!-- Divider / Space -->
        <div class="mt-8 border-t border-slate-100 pt-6 text-center">
            <p class="text-xs text-slate-400 font-medium">
                &copy; 2026 Badan Pusat Statistik
            </p>
        </div>
    </div>


</body>
</html>
