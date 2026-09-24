<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Google Notes') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @php
            $theme = auth()->check() ? auth()->user()->getOrCreateSettings()->theme : 'light';
        @endphp
        <script>
            const theme = "{{ $theme }}";
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <style>
            :root {
                --google-blue: #4285F4;
                --google-red: #EA4335;
                --google-yellow: #FBBC05;
                --google-green: #34A853;
                --google-gray-bg: #F8F9FA;
            }

            body { background-color: var(--google-gray-bg); }
            .dark body { background-color: #111827; }

            /* Sidebar clean Google style */
            .warm-sidebar {
                background: #ffffff;
                border-right: 1px solid #e5e7eb;
            }
            .dark .warm-sidebar {
                background: #1f2937;
                border-right: 1px solid #374151;
            }

            /* Nav active state — Google Blue pill */
            .nav-active {
                background: rgba(66, 133, 244, 0.1);
                color: #1a73e8 !important;
                font-weight: 600;
            }
            .dark .nav-active {
                background: rgba(66, 133, 244, 0.2);
                color: #60a5fa !important;
            }

            /* Sidebar Logo Google Blue */
            .sidebar-logo {
                background: #4285F4;
                box-shadow: 0 2px 8px rgba(66,133,244,0.3);
            }

            /* Top header clean */
            .warm-header {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid #e5e7eb;
            }
            .dark .warm-header {
                background: rgba(31, 41, 55, 0.9);
                border-bottom: 1px solid #374151;
            }

            /* AI Chat Panel */
            .ai-chat-panel {
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 9000;
            }

            .ai-chat-bubble {
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #4285F4;
                box-shadow: 0 4px 20px rgba(66,133,244,0.4);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                color: white;
            }
            .ai-chat-bubble:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 28px rgba(66,133,244,0.5);
            }

            .ai-chat-window {
                position: absolute;
                bottom: 70px;
                right: 0;
                width: 360px;
                max-height: 520px;
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                border: 1px solid #e5e7eb;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                animation: slideUp 0.25s ease;
            }
            .dark .ai-chat-window {
                background: #1f2937;
                border-color: #374151;
                box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(16px) scale(0.95); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }

            .chat-messages {
                flex: 1;
                overflow-y: auto;
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-height: 360px;
            }

            .chat-msg-user {
                align-self: flex-end;
                background: #4285F4;
                color: white;
                padding: 10px 14px;
                border-radius: 16px 16px 4px 16px;
                max-width: 80%;
                font-size: 13px;
                line-height: 1.5;
                word-wrap: break-word;
            }

            .chat-msg-ai {
                align-self: flex-start;
                background: #f1f3f4;
                color: #202124;
                padding: 10px 14px;
                border-radius: 16px 16px 16px 4px;
                max-width: 85%;
                font-size: 13px;
                line-height: 1.5;
                word-wrap: break-word;
                border: 1px solid #e5e7eb;
            }
            .dark .chat-msg-ai {
                background: #374151;
                color: #f3f4f6;
                border-color: #4b5563;
            }

            .chat-msg-ai.rudy {
                background: #fef2f2;
                border-color: #fecaca;
                color: #991b1b;
            }

            .chat-input-area {
                padding: 12px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                gap: 8px;
                align-items: flex-end;
            }
            .dark .chat-input-area {
                border-top-color: #374151;
            }

            .chat-input {
                flex: 1;
                padding: 10px 14px;
                border: 1.5px solid #e5e7eb;
                border-radius: 14px;
                font-size: 13px;
                background: #f8f9fa;
                color: #202124;
                resize: none;
                outline: none;
                font-family: inherit;
                max-height: 100px;
                transition: border-color 0.2s;
            }
            .chat-input:focus { border-color: #4285F4; }
            .dark .chat-input {
                background: #374151;
                color: #f3f4f6;
                border-color: #4b5563;
            }

            .chat-send-btn {
                width: 38px;
                height: 38px;
                border-radius: 50%;
                background: #4285F4;
                border: none;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s;
                flex-shrink: 0;
            }
            .chat-send-btn:hover { transform: scale(1.05); background: #3367d6; }
            .chat-send-btn:disabled { opacity: 0.5; cursor: not-allowed; }

            /* Scrollbar */
            .chat-messages::-webkit-scrollbar { width: 4px; }
            .chat-messages::-webkit-scrollbar-track { background: transparent; }
            .chat-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

            /* Dot pulse loader for chat */
            .dot-pulse {
                display: flex;
                gap: 4px;
                align-items: center;
                padding: 10px 14px;
            }
            .dot-pulse span {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #4285F4;
                animation: pulse-dot 1.2s infinite;
            }
            .dot-pulse span:nth-child(2) { animation-delay: 0.2s; }
            .dot-pulse span:nth-child(3) { animation-delay: 0.4s; }
            @keyframes pulse-dot {
                0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
                40% { transform: scale(1); opacity: 1; }
            }
        </style>
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen bg-[#F8F9FA] dark:bg-gray-900">

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-black/50 lg:hidden">
            </div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="warm-sidebar fixed inset-y-0 left-0 z-50 w-64 shadow-sm transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">

                <!-- Sidebar Header -->
                <div class="flex items-center gap-3 px-6 h-16 border-b border-amber-100 dark:border-stone-800 shrink-0">
                    <div class="sidebar-logo flex items-center justify-center w-9 h-9 rounded-xl">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-amber-800 dark:text-amber-200">Google Notes</span>

                    <!-- Close button (mobile) -->
                    <button @click="sidebarOpen = false" class="ml-auto lg:hidden p-1 rounded-lg hover:bg-amber-100 dark:hover:bg-stone-700 transition-colors">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('dashboard') ? 'nav-active' : 'text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 hover:text-amber-900 dark:hover:text-stone-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Dashboard
                    </a>

                    <!-- Notes -->
                    <a href="{{ route('notes.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('notes.index') || request()->routeIs('notes.create') || request()->routeIs('notes.edit') || request()->routeIs('notes.show') ? 'nav-active' : 'text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 hover:text-amber-900 dark:hover:text-stone-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        Notes
                    </a>

                    <!-- Archived -->
                    <a href="{{ route('notes.archive-list') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('notes.archive-list') ? 'nav-active' : 'text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 hover:text-amber-900 dark:hover:text-stone-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        Archived
                    </a>

                    <!-- Tags -->
                    <a href="{{ route('tags.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('tags.*') ? 'nav-active' : 'text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 hover:text-amber-900 dark:hover:text-stone-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v13.5A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V14.4m-12-3h12" />
                        </svg>
                        Tags
                    </a>

                    <!-- Trash -->
                    <a href="{{ route('notes.trash-list') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ request()->routeIs('notes.trash-list') ? 'nav-active' : 'text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 hover:text-amber-900 dark:hover:text-stone-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Trash
                    </a>

                    <!-- Profile -->
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ (request()->routeIs('profile.*') || request()->routeIs('settings.*')) ? 'nav-active' : 'text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 hover:text-amber-900 dark:hover:text-stone-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Profile
                    </a>
                </nav>

                <!-- Sidebar Footer -->
                <div class="px-3 py-4 border-t border-amber-100 dark:border-stone-800 shrink-0">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-amber-700 dark:text-stone-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 transition-all duration-200 w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:ml-64">
                <!-- Top Navigation -->
                <header class="warm-header sticky top-0 z-30">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <!-- Mobile Menu Button -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-amber-100 dark:hover:bg-stone-700 transition-colors">
                            <svg class="w-6 h-6 text-amber-700 dark:text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </button>

                        <!-- Page Title -->
                        <div class="hidden lg:block">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>

                        <!-- Right Side -->
                        <div class="flex items-center gap-4 ml-auto" x-data="{ profileOpen: false }">
                            <!-- User Dropdown -->
                            <div class="relative">
                                <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-amber-50 dark:hover:bg-stone-700 transition-all duration-200">
                                    @if (Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full ring-2 ring-amber-200 dark:ring-stone-600 object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold" style="background: linear-gradient(135deg, #f59e0b, #e8621a);">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="hidden sm:block text-left">
                                        <p class="text-sm font-semibold text-amber-900 dark:text-stone-100">{{ Auth::user()->display_name ?? Auth::user()->name }}</p>
                                        <p class="text-xs text-amber-500 dark:text-stone-400">{{ Auth::user()->email }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-amber-400 dark:text-stone-500 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="profileOpen" x-cloak
                                     @click.outside="profileOpen = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-stone-800 rounded-xl shadow-lg border border-amber-100 dark:border-stone-700 py-2 z-50">
                                    <div class="px-4 py-2 border-b border-amber-50 dark:border-stone-700">
                                        <p class="text-sm font-semibold text-amber-900 dark:text-stone-100">{{ Auth::user()->display_name ?? Auth::user()->name }}</p>
                                        <p class="text-xs text-amber-400 dark:text-stone-500">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-amber-700 dark:text-stone-300 hover:bg-amber-50 dark:hover:bg-stone-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-3 px-4 py-2.5 text-sm text-amber-700 dark:text-stone-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 transition-colors w-full">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-4 sm:p-6 lg:p-8">
                    <!-- Global Toast Notifications Container -->
                    <div x-data="{
                            toasts: [],
                            addToast(message, type = 'success') {
                                const id = Date.now();
                                this.toasts.push({ id, message, type });
                                setTimeout(() => this.removeToast(id), 5000);
                            },
                            removeToast(id) {
                                this.toasts = this.toasts.filter(t => t.id !== id);
                            }
                         }"
                         @toast.window="addToast($event.detail.message, $event.detail.type)"
                         class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none">
                        
                        <template x-for="toast in toasts" :key="toast.id">
                            <div x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-0 sm:translate-x-2"
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="flex items-center gap-3 p-4 rounded-xl shadow-lg border pointer-events-auto transition-all"
                                 :class="{
                                    'bg-green-50 dark:bg-green-900/30 border-green-200 dark:border-green-800 text-green-800 dark:text-green-300': toast.type === 'success',
                                    'bg-red-50 dark:bg-red-900/30 border-red-200 dark:border-red-800 text-red-800 dark:text-red-300': toast.type === 'error',
                                    'bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300': toast.type === 'warning',
                                    'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300': toast.type === 'info',
                                 }">
                                
                                <template x-if="toast.type === 'success'">
                                    <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </template>
                                <template x-if="toast.type === 'error'">
                                    <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                                    </svg>
                                </template>
                                <template x-if="toast.type === 'warning'">
                                    <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </template>
                                <template x-if="toast.type === 'info'">
                                    <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.854l-.555 1.658a2.25 2.25 0 002.506 2.937l.04-.02a.75.75 0 11.754 1.302l-.04.02a3.75 3.75 0 01-4.177-4.897l.555-1.658a.75.75 0 00-1.063-.852l-.04.02a.75.75 0 11-.75-1.306l.04-.02zM12 7.5a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </template>

                                <div class="text-sm font-medium flex-1 text-gray-800 dark:text-gray-100" x-text="toast.message"></div>
                                <button type="button" @click="removeToast(toast.id)" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    @if(session('success'))
                        <div x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ session('success') }}', type: 'success' } }))"></div>
                    @endif
                    @if(session('error'))
                        <div x-init="window.dispatchEvent(new CustomEvent('toast', { detail: { message: '{{ session('error') }}', type: 'error' } }))"></div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- AI Chat Agent Floating Panel (with Voice Mode) -->
        <style>
            /* Voice mode styles */
            @keyframes voicePulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.5); }
                50%       { box-shadow: 0 0 0 14px rgba(245,158,11,0); }
            }
            @keyframes voiceSpeaking {
                0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0.5); }
                50%       { box-shadow: 0 0 0 12px rgba(99,102,241,0); }
            }
            .mic-btn-listening {
                background: linear-gradient(135deg, #ef4444, #dc2626) !important;
                animation: voicePulse 1s infinite;
            }
            .mic-btn-speaking {
                background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
                animation: voiceSpeaking 1.2s infinite;
            }
            .voice-wave {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 3px;
                padding: 8px 14px;
                align-self: flex-start;
            }
            .voice-wave span {
                width: 3px;
                border-radius: 3px;
                background: #f59e0b;
                animation: waveBar 0.8s ease-in-out infinite;
            }
            .voice-wave span:nth-child(1) { height: 12px; animation-delay: 0s; }
            .voice-wave span:nth-child(2) { height: 20px; animation-delay: 0.1s; }
            .voice-wave span:nth-child(3) { height: 16px; animation-delay: 0.2s; }
            .voice-wave span:nth-child(4) { height: 24px; animation-delay: 0.15s; }
            .voice-wave span:nth-child(5) { height: 14px; animation-delay: 0.05s; }
            @keyframes waveBar {
                0%, 100% { transform: scaleY(0.5); opacity: 0.6; }
                50%       { transform: scaleY(1);   opacity: 1; }
            }
            .speaking-indicator {
                display: inline-flex;
                align-items: center;
                gap: 3px;
                font-size: 11px;
                color: #6366f1;
                padding: 4px 10px;
                background: rgba(99,102,241,0.08);
                border-radius: 20px;
                border: 1px solid rgba(99,102,241,0.2);
                align-self: flex-start;
                margin-top: 2px;
            }
            .speaking-indicator span {
                width: 3px; height: 3px;
                border-radius: 50%;
                background: #6366f1;
                animation: pulse-dot 0.8s infinite;
            }
            .speaking-indicator span:nth-child(2) { animation-delay: 0.2s; }
            .speaking-indicator span:nth-child(3) { animation-delay: 0.4s; }
            .voice-toggle-btn {
                width: 28px; height: 28px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
                font-size: 13px;
            }
            .voice-banner {
                padding: 6px 14px;
                background: linear-gradient(90deg, rgba(239,68,68,0.08), rgba(239,68,68,0.03));
                border-bottom: 1px solid rgba(239,68,68,0.12);
                display: flex;
                align-items: center;
                gap: 8px;
                flex-shrink: 0;
                font-size: 12px;
                color: #ef4444;
                font-weight: 500;
            }
        </style>

        <div class="ai-chat-panel" x-data="{
            chatOpen: false,
            messages: [],
            inputText: '',
            loading: false,
            csrfToken: '{{ csrf_token() }}',

            /* --- Voice state --- */
            voiceMode: false,
            isListening: false,
            isSpeaking: false,
            recognition: null,
            synth: window.speechSynthesis,
            voiceSupported: ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window),
            speechSupported: ('speechSynthesis' in window),
            autoSpeak: true,
            currentUtterance: null,
            transcript: '',

            get history() {
                return this.messages.map(m => ({ role: m.role, text: m.text }));
            },

            /* Init speech recognition */
            initRecognition() {
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SR) return;
                this.recognition = new SR();
                this.recognition.continuous = false;
                this.recognition.interimResults = true;
                this.recognition.lang = 'id-ID';

                this.recognition.onstart = () => {
                    this.isListening = true;
                    this.transcript = '';
                };
                this.recognition.onresult = (e) => {
                    let interim = '';
                    let final = '';
                    for (let i = e.resultIndex; i < e.results.length; i++) {
                        const t = e.results[i][0].transcript;
                        if (e.results[i].isFinal) final += t;
                        else interim += t;
                    }
                    this.transcript = final || interim;
                    this.inputText = this.transcript;
                };
                this.recognition.onend = () => {
                    this.isListening = false;
                    if (this.transcript.trim()) {
                        this.$nextTick(() => this.sendMessage());
                    }
                    this.transcript = '';
                };
                this.recognition.onerror = (e) => {
                    this.isListening = false;
                    console.warn('Speech recognition error:', e.error);
                };
            },

            /* Toggle mic listen */
            toggleListen() {
                if (!this.recognition) this.initRecognition();
                if (!this.recognition) return;

                if (this.isListening) {
                    this.recognition.stop();
                    return;
                }

                /* Stop any ongoing speech before listening */
                if (this.isSpeaking) this.stopSpeaking();

                try {
                    this.recognition.start();
                } catch(e) { /* already started */ }
            },

            /* Speak text aloud */
            speak(text) {
                if (!this.speechSupported || !this.autoSpeak) return;
                if (this.synth.speaking) this.synth.cancel();

                /* Strip markdown-style characters for cleaner TTS */
                const clean = text
                    .replace(/#{1,6}\s/g, '')
                    .replace(/\*\*/g, '')
                    .replace(/\*/g, '')
                    .replace(/`{1,3}/g, '')
                    .replace(/[-•]\s/g, '. ')
                    .trim();

                const utter = new SpeechSynthesisUtterance(clean);
                // Fast & snappy sarcastic tone: faster speech rate, sharp natural pitch
                utter.rate = 1.15;
                utter.pitch = 0.85;
                utter.volume = 1;
                utter.lang = 'id-ID';

                /* STRICTLY pick Indonesian voice first so it speaks natural Indonesian */
                const voices = this.synth.getVoices();
                const idVoice = voices.find(v => v.lang === 'id-ID' || v.lang === 'id_ID' || v.lang.startsWith('id') || v.name.toLowerCase().includes('indonesi'))
                    || voices.find(v => v.lang.includes('id'))
                    || null;
                
                if (idVoice) {
                    utter.voice = idVoice;
                    utter.lang = 'id-ID';
                } else {
                    utter.lang = 'id-ID';
                }

                utter.onstart  = () => { this.isSpeaking = true; };
                utter.onend    = () => { this.isSpeaking = false; this.currentUtterance = null; };
                utter.onerror  = () => { this.isSpeaking = false; this.currentUtterance = null; };

                this.currentUtterance = utter;
                this.synth.speak(utter);
            },

            stopSpeaking() {
                this.synth.cancel();
                this.isSpeaking = false;
                this.currentUtterance = null;
            },

            /* Send chat message */
            async sendMessage() {
                const text = this.inputText.trim();
                if (!text || this.loading) return;
                this.inputText = '';

                /* Stop speaking if AI was talking */
                if (this.isSpeaking) this.stopSpeaking();

                this.messages.push({ role: 'user', text });
                this.loading = true;
                this.$nextTick(() => this.scrollToBottom());

                try {
                    const res = await fetch('/ai/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message: text, history: this.history.slice(0, -1) })
                    });
                    const data = await res.json();
                    const reply = data.result || data.error || 'Something went wrong. Try again.';
                    this.messages.push({ role: 'model', text: reply });

                    /* Auto-speak AI reply in voice mode */
                    if (this.voiceMode && this.autoSpeak) {
                        this.$nextTick(() => this.speak(reply));
                    }
                } catch(e) {
                    const errMsg = 'Connection error. Please try again.';
                    this.messages.push({ role: 'model', text: errMsg });
                    if (this.voiceMode && this.autoSpeak) this.speak(errMsg);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => this.scrollToBottom());
                }
            },

            scrollToBottom() {
                const el = this.$refs.chatMessages;
                if (el) el.scrollTop = el.scrollHeight;
            },
            handleKey(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            }
        }" x-init="if (voiceSupported) initRecognition()">

            <!-- Chat Window -->
            <div x-show="chatOpen" x-cloak class="ai-chat-window">

                <!-- Header -->
                <div style="background: #4285F4; padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                    <div style="width:32px;height:32px;background:rgba(255,255,255,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:18px;height:18px;color:white;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <p style="color:white;font-size:14px;font-weight:600;margin:0;">AI Assistant</p>
                        <p style="color:rgba(255,255,255,0.8);font-size:11px;margin:0;" x-text="voiceMode ? '🎙️ Voice Mode ON' : 'Powered by Gemini'"></p>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <!-- Voice Mode Toggle -->
                        <template x-if="voiceSupported">
                            <button @click="voiceMode = !voiceMode; if(!voiceMode && isSpeaking) stopSpeaking(); if(!voiceMode && isListening) recognition.stop()"
                                    :title="voiceMode ? 'Matikan Voice Mode' : 'Nyalakan Voice Mode'"
                                    class="voice-toggle-btn"
                                    :style="voiceMode ? 'background:white;color:#4285F4;' : 'background:rgba(255,255,255,0.15);color:white;'">
                                🎙️
                            </button>
                        </template>
                        <!-- Auto-Speak Toggle (only when voice mode ON) -->
                        <template x-if="voiceMode && speechSupported">
                            <button @click="autoSpeak = !autoSpeak; if(!autoSpeak && isSpeaking) stopSpeaking()"
                                    :title="autoSpeak ? 'Matikan suara AI' : 'Hidupkan suara AI'"
                                    class="voice-toggle-btn"
                                    :style="autoSpeak ? 'background:white;color:#34A853;' : 'background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.5);'">
                                🔊
                            </button>
                        </template>
                        <button @click="chatOpen = false; if(isSpeaking) stopSpeaking(); if(isListening) recognition.stop()"
                                style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;padding:4px 8px;color:white;cursor:pointer;font-size:18px;line-height:1;">×</button>
                    </div>
                </div>

                <!-- Listening Banner -->
                <div x-show="isListening" class="voice-banner">
                    <span style="font-size:16px;animation:pulse-dot 0.8s infinite;">🔴</span>
                    Mendengarkan... <span style="font-style:italic;opacity:0.8;" x-text="'\"' + (inputText || '...') + '\"'"></span>
                </div>

                <!-- Welcome message -->
                <div x-show="messages.length === 0" class="chat-messages" style="justify-content: center; align-items: center;">
                    <div style="text-align:center; padding: 20px 10px;">
                        <div style="font-size:36px;margin-bottom:12px;" x-text="voiceMode ? '🎙️' : '🤖'"></div>
                        <p style="font-size:14px;font-weight:600;color:#202124;margin-bottom:6px;" x-text="voiceMode ? 'Voice Mode Aktif!' : 'Halo! Saya AI Assistant'"></p>
                        <p style="font-size:12px;color:#5f6368;" x-text="voiceMode ? 'Tekan mic lalu bicara — AI akan menjawab dengan suara 🎤' : 'Tanyakan apa saja — ide, pertanyaan, brainstorm, atau apapun.'"></p>
                    </div>
                </div>

                <!-- Messages -->
                <div x-show="messages.length > 0" class="chat-messages" x-ref="chatMessages">
                    <template x-for="(msg, i) in messages" :key="i">
                        <div style="display:flex;flex-direction:column;align-items:inherit;">
                            <div :class="msg.role === 'user' ? 'chat-msg-user' : 'chat-msg-ai'" x-text="msg.text"></div>
                            <!-- Replay voice button on AI messages -->
                            <template x-if="msg.role === 'model' && speechSupported">
                                <button @click="speak(msg.text)"
                                        style="align-self:flex-start;margin-top:3px;background:none;border:none;cursor:pointer;font-size:11px;color:#b45309;opacity:0.6;padding:2px 6px;border-radius:8px;transition:opacity 0.2s;"
                                        onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.6'"
                                        title="Putar ulang suara">
                                    🔊 Putar ulang
                                </button>
                            </template>
                        </div>
                    </template>

                    <!-- Loading dots -->
                    <div x-show="loading" class="dot-pulse" style="align-self:flex-start;">
                        <span></span><span></span><span></span>
                    </div>

                    <!-- Speaking indicator -->
                    <div x-show="isSpeaking" class="speaking-indicator">
                        <span></span><span></span><span></span>
                        AI sedang berbicara...
                        <button @click="stopSpeaking()" style="background:none;border:none;cursor:pointer;font-size:11px;color:#ef4444;margin-left:4px;" title="Stop">⏹</button>
                    </div>

                    <!-- Listening wave indicator -->
                    <div x-show="isListening" class="voice-wave">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="chat-input-area">
                    <textarea
                        class="chat-input"
                        x-model="inputText"
                        @keydown="handleKey($event)"
                        :disabled="loading || isListening"
                        :placeholder="isListening ? 'Mendengarkan...' : voiceMode ? 'Ketik atau tekan 🎤 untuk berbicara...' : 'Ketik pesan... (Enter kirim)'"
                        rows="1"
                        @input="$el.style.height='auto'; $el.style.height=$el.scrollHeight+'px'"
                    ></textarea>

                    <!-- Mic Button (shown always, prominent in voice mode) -->
                    <template x-if="voiceSupported">
                        <button @click="toggleListen()"
                                :disabled="loading"
                                :title="isListening ? 'Stop mendengarkan' : 'Mulai bicara'"
                                :class="isListening ? 'mic-btn-listening' : isSpeaking ? 'mic-btn-speaking' : ''"
                                style="width:38px;height:38px;border-radius:50%;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all 0.3s;flex-shrink:0;"
                                :style="!isListening && !isSpeaking ? 'background: linear-gradient(135deg, ' + (voiceMode ? '#f59e0b,#d97706' : 'rgba(0,0,0,0.08),rgba(0,0,0,0.12)') + ');' : ''">
                            <svg style="width:16px;height:16px;" :style="voiceMode || isListening ? 'color:white' : 'color:#9ca3af'" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                                <path d="M19 10v2a7 7 0 0 1-14 0v-2H3v2a9 9 0 0 0 8 8.94V23h2v-2.06A9 9 0 0 0 21 12v-2h-2z"/>
                            </svg>
                        </button>
                    </template>

                    <!-- Send Button -->
                    <button class="chat-send-btn" @click="sendMessage" :disabled="loading || !inputText.trim() || isListening">
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Floating Chat Toggle Button -->
            <button class="ai-chat-bubble"
                    @click="chatOpen = !chatOpen; if(!chatOpen && isSpeaking) stopSpeaking(); if(!chatOpen && isListening) recognition.stop()"
                    :class="isListening ? 'mic-btn-listening' : isSpeaking ? 'mic-btn-speaking' : ''"
                    title="Chat dengan AI">
                <!-- Mic wave icon when listening -->
                <template x-if="isListening">
                    <svg style="width:24px;height:24px;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2H3v2a9 9 0 0 0 8 8.94V23h2v-2.06A9 9 0 0 0 21 12v-2h-2z"/>
                    </svg>
                </template>
                <!-- Speaker icon when AI is speaking -->
                <template x-if="!isListening && isSpeaking">
                    <svg style="width:24px;height:24px;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                        <path d="M14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                    </svg>
                </template>
                <!-- Default icons -->
                <template x-if="!isListening && !isSpeaking">
                    <svg x-show="!chatOpen" style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" />
                    </svg>
                </template>
                <svg x-show="chatOpen && !isListening && !isSpeaking" style="width:24px;height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

    </body>
</html>