<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Google Notes - A simple and beautiful note-taking app powered by Google. Organize your thoughts, capture ideas, and stay productive.">

    <title>{{ config('app.name', 'Google Notes') }} - Capture Your Ideas</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F8F9FA] text-[#202124]">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 bg-[#4285F4] rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-[#202124]">Google Notes</span>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="#features" class="text-sm font-medium text-gray-600 hover:text-[#4285F4] transition-colors duration-300">Features</a>
                    <a href="#about" class="text-sm font-medium text-gray-600 hover:text-[#4285F4] transition-colors duration-300">About</a>
                    <a href="{{ route('google.redirect') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#4285F4] text-white text-sm font-medium rounded-full hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200 transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg x-show="!mobileOpen" class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden pb-4 border-t border-gray-100 mt-2 pt-4">
                <div class="flex flex-col gap-3">
                    <a href="#features" @click="mobileOpen = false" class="text-sm font-medium text-gray-600 hover:text-[#4285F4] px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Features</a>
                    <a href="#about" @click="mobileOpen = false" class="text-sm font-medium text-gray-600 hover:text-[#4285F4] px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">About</a>
                    <a href="{{ route('google.redirect') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#4285F4] text-white text-sm font-medium rounded-full hover:bg-[#3367D6] transition-all duration-300">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Continue with Google
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center pt-16 overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#4285F4]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-[#34A853]/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-[#FBBC05]/5 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full shadow-sm border border-gray-100 mb-8">
                <span class="flex h-2 w-2 rounded-full bg-[#34A853]"></span>
                <span class="text-xs font-medium text-gray-600">Simple & Secure Note Taking</span>
            </div>

            <!-- Heading -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold tracking-tight text-[#202124] mb-6 leading-tight">
                Capture your ideas with
                <span class="relative">
                    <span class="bg-gradient-to-r from-[#4285F4] via-[#34A853] to-[#FBBC05] bg-clip-text text-transparent">Google Notes</span>
                </span>
            </h1>

            <!-- Subtitle -->
            <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                A simple, beautiful note-taking app. Sign in with your Google account and start organizing your thoughts instantly.
            </p>

            <!-- CTA -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('google.redirect') }}" class="group inline-flex items-center gap-3 px-8 py-4 bg-[#4285F4] text-white font-semibold rounded-full hover:bg-[#3367D6] hover:shadow-xl hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Continue with Google
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
                <a href="#features" class="inline-flex items-center gap-2 px-6 py-3.5 text-gray-600 font-medium hover:text-[#4285F4] transition-colors duration-300">
                    Learn more
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                    </svg>
                </a>
            </div>

            <!-- Error Message -->
            @if (session('error'))
                <div class="mt-8 inline-flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 lg:py-28 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-[#202124] mb-4">Everything you need</h2>
                <p class="text-lg text-gray-500 max-w-2xl mx-auto">Simple yet powerful features to keep your notes organized and accessible.</p>
            </div>

            <!-- Feature Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 bg-[#F8F9FA] rounded-2xl hover:bg-white hover:shadow-xl hover:shadow-gray-100/80 transition-all duration-500 border border-transparent hover:border-gray-100">
                    <div class="w-12 h-12 bg-[#4285F4]/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#202124] mb-2">Secure Login</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Sign in securely with your Google account. No passwords to remember, no manual registration needed.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-8 bg-[#F8F9FA] rounded-2xl hover:bg-white hover:shadow-xl hover:shadow-gray-100/80 transition-all duration-500 border border-transparent hover:border-gray-100">
                    <div class="w-12 h-12 bg-[#34A853]/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#202124] mb-2">Easy Note Taking</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Create, edit, and delete notes effortlessly. Your thoughts, beautifully organized in one place.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-8 bg-[#F8F9FA] rounded-2xl hover:bg-white hover:shadow-xl hover:shadow-gray-100/80 transition-all duration-500 border border-transparent hover:border-gray-100">
                    <div class="w-12 h-12 bg-[#FBBC05]/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-[#FBBC05]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#202124] mb-2">Instant Search</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Find any note instantly with real-time search. Search by title or content — results appear as you type.</p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-8 bg-[#F8F9FA] rounded-2xl hover:bg-white hover:shadow-xl hover:shadow-gray-100/80 transition-all duration-500 border border-transparent hover:border-gray-100">
                    <div class="w-12 h-12 bg-[#EA4335]/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-[#EA4335]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#202124] mb-2">Archive & Organize</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Archive notes you don't need right now. Unarchive them anytime — nothing is ever lost.</p>
                </div>

                <!-- Feature 5 -->
                <div class="group p-8 bg-[#F8F9FA] rounded-2xl hover:bg-white hover:shadow-xl hover:shadow-gray-100/80 transition-all duration-500 border border-transparent hover:border-gray-100">
                    <div class="w-12 h-12 bg-[#4285F4]/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#202124] mb-2">Responsive Design</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Access your notes from any device. Beautiful on desktop, tablet, and mobile screens.</p>
                </div>

                <!-- Feature 6 -->
                <div class="group p-8 bg-[#F8F9FA] rounded-2xl hover:bg-white hover:shadow-xl hover:shadow-gray-100/80 transition-all duration-500 border border-transparent hover:border-gray-100">
                    <div class="w-12 h-12 bg-[#34A853]/10 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-[#202124] mb-2">Personal Dashboard</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Your personalized dashboard with statistics, recent notes, and quick actions all in one view.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About / CTA Section -->
    <section id="about" class="py-20 lg:py-28 bg-[#F8F9FA]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white rounded-3xl p-10 sm:p-16 shadow-sm border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-[#4285F4] to-[#34A853] rounded-2xl flex items-center justify-center mx-auto mb-8">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <h2 class="text-3xl sm:text-4xl font-bold text-[#202124] mb-4">Ready to get started?</h2>
                <p class="text-lg text-gray-500 mb-10 max-w-xl mx-auto leading-relaxed">
                    Join Google Notes today. No registration forms, no passwords to remember. Just sign in with Google and start taking notes.
                </p>
                <a href="{{ route('google.redirect') }}" class="group inline-flex items-center gap-3 px-10 py-4 bg-[#4285F4] text-white font-semibold rounded-full hover:bg-[#3367D6] hover:shadow-xl hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-1">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Get Started — It's Free
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-8 h-8 bg-[#4285F4] rounded-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <span class="font-semibold text-[#202124]">Google Notes</span>
                </div>

                <!-- Copyright -->
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Google Notes. Built with Laravel.</p>

                <!-- Links -->
                <div class="flex items-center gap-6">
                    <a href="#features" class="text-sm text-gray-400 hover:text-[#4285F4] transition-colors">Features</a>
                    <a href="#about" class="text-sm text-gray-400 hover:text-[#4285F4] transition-colors">About</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
