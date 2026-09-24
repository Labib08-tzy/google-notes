<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Settings</h1>
    </x-slot>

    <!-- Success message -->
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl text-sm"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <!-- Navigation Tabs to Settings -->
        <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-px mb-6">
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                Profile Overview
            </a>
            <a href="{{ route('settings.edit') }}" class="px-4 py-2 text-sm font-semibold text-[#4285F4] border-b-2 border-[#4285F4] -mb-px">
                App & AI Settings
            </a>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- 1. Appearance Settings Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
                <h2 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-2">Appearance Theme</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Choose how Google Notes looks on your device.</p>

                <div class="grid grid-cols-3 gap-4">
                    <!-- Light Mode option -->
                    <label class="relative flex flex-col items-center gap-2 p-4 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                        <input type="radio" name="theme" value="light" class="absolute top-4 right-4 text-[#4285F4] focus:ring-[#4285F4]" {{ $settings->theme === 'light' ? 'checked' : '' }}>
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-300 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                        </svg>
                        <span class="text-xs font-semibold mt-1 text-[#202124] dark:text-gray-100">Light Mode</span>
                    </label>

                    <!-- Dark Mode option -->
                    <label class="relative flex flex-col items-center gap-2 p-4 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                        <input type="radio" name="theme" value="dark" class="absolute top-4 right-4 text-[#4285F4] focus:ring-[#4285F4]" {{ $settings->theme === 'dark' ? 'checked' : '' }}>
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-300 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span class="text-xs font-semibold mt-1 text-[#202124] dark:text-gray-100">Dark Mode</span>
                    </label>

                    <!-- System option -->
                    <label class="relative flex flex-col items-center gap-2 p-4 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-2xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                        <input type="radio" name="theme" value="system" class="absolute top-4 right-4 text-[#4285F4] focus:ring-[#4285F4]" {{ $settings->theme === 'system' ? 'checked' : '' }}>
                        <svg class="w-8 h-8 text-gray-500 dark:text-gray-300 mt-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs font-semibold mt-1 text-[#202124] dark:text-gray-100">System</span>
                    </label>
                </div>
                @error('theme')
                    <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. AI Preferences Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
                <h2 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-2">Gemini AI Assistant Preferences</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Customize settings for all Gemini note tools.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Preferred Language -->
                    <div>
                        <label for="ai_language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred AI Language</label>
                        <select name="ai_language" id="ai_language" class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                            <option value="auto" {{ $settings->ai_language === 'auto' ? 'selected' : '' }}>Auto-Detect Language</option>
                            <option value="id" {{ $settings->ai_language === 'id' ? 'selected' : '' }}>Indonesian (Bahasa Indonesia)</option>
                            <option value="en" {{ $settings->ai_language === 'en' ? 'selected' : '' }}>English</option>
                        </select>
                        @error('ai_language')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- AI Response Length -->
                    <div>
                        <label for="ai_response_length" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Output Length</label>
                        <select name="ai_response_length" id="ai_response_length" class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                            <option value="short" {{ $settings->ai_response_length === 'short' ? 'selected' : '' }}>Short & Direct</option>
                            <option value="medium" {{ $settings->ai_response_length === 'medium' ? 'selected' : '' }}>Medium Details</option>
                            <option value="long" {{ $settings->ai_response_length === 'long' ? 'selected' : '' }}>Long & Detailed</option>
                        </select>
                        @error('ai_response_length')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- AI Tone -->
                    <div>
                        <label for="ai_tone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Writing Tone</label>
                        <select name="ai_tone" id="ai_tone" class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                            <option value="friendly" {{ $settings->ai_tone === 'friendly' ? 'selected' : '' }}>Friendly & Welcoming</option>
                            <option value="professional" {{ $settings->ai_tone === 'professional' ? 'selected' : '' }}>Professional & Business</option>
                            <option value="casual" {{ $settings->ai_tone === 'casual' ? 'selected' : '' }}>Casual & Fun</option>
                            <option value="academic" {{ $settings->ai_tone === 'academic' ? 'selected' : '' }}>Academic & Technical</option>
                            <option value="rudy" {{ $settings->ai_tone === 'rudy' ? 'selected' : '' }}>😈 Rudy Mode — Brutal & Sarcastic</option>
                        </select>
                        @if($settings->ai_tone === 'rudy')
                            <p class="mt-2 text-xs text-red-500 dark:text-red-400 font-medium">⚠️ Rudy Mode aktif. AI akan sangat blak-blakan, sarkastis, dan tidak sopan — seperti teman jujur yang nyebelin tapi selalu bener.</p>
                        @endif
                        @error('ai_tone')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 3. General Settings Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
                <h2 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-2">General & Layout Settings</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Configure layout options and list behaviors.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Dashboard Layout -->
                    <div>
                        <label for="dashboard_layout" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default View Layout</label>
                        <select name="dashboard_layout" id="dashboard_layout" class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                            <option value="grid" {{ $settings->dashboard_layout === 'grid' ? 'selected' : '' }}>Grid Layout (Cards)</option>
                            <option value="list" {{ $settings->dashboard_layout === 'list' ? 'selected' : '' }}>List Layout (Rows)</option>
                        </select>
                        @error('dashboard_layout')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes Per Page -->
                    <div>
                        <label for="notes_per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes Per Page</label>
                        <input type="number" name="notes_per_page" id="notes_per_page" value="{{ old('notes_per_page', $settings->notes_per_page) }}" required min="1" max="100"
                               class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                        @error('notes_per_page')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Default Note Sorting -->
                    <div>
                        <label for="default_sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default Note Sorting</label>
                        <select name="default_sort" id="default_sort" class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                            <option value="latest" {{ $settings->default_sort === 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="oldest" {{ $settings->default_sort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="title_asc" {{ $settings->default_sort === 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                            <option value="title_desc" {{ $settings->default_sort === 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                        </select>
                        @error('default_sort')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50 dark:border-gray-700">
                <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-app-layout>