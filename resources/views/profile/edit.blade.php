<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">User Profile</h1>
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

    <div class="max-w-4xl mx-auto space-y-6" x-data="{ deleteModalOpen: false }">
        <!-- Navigation Tabs to Settings -->
        <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-px mb-6">
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 text-sm font-semibold text-[#4285F4] border-b-2 border-[#4285F4] -mb-px">
                Profile Overview
            </a>
            <a href="{{ route('settings.edit') }}" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                App & AI Settings
            </a>
        </div>

        <!-- 1. Profile Overview Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-[#202124] dark:text-gray-100 mb-6">Profile Overview</h2>
            
            <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                <!-- Avatar display -->
                <div class="shrink-0">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full shadow-md object-cover ring-4 ring-gray-50 dark:ring-gray-700">
                    @else
                        <div class="w-24 h-24 rounded-full bg-[#4285F4] flex items-center justify-center text-white text-4xl font-semibold shadow-md ring-4 ring-gray-50 dark:ring-gray-700">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <!-- Info list -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">Full Name</p>
                        <p class="text-sm font-semibold text-[#202124] dark:text-gray-100 mt-1">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">Preferred Display Name</p>
                        <p class="text-sm font-semibold text-[#202124] dark:text-gray-100 mt-1">{{ $user->display_name ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">Email Address</p>
                        <p class="text-sm font-semibold text-[#202124] dark:text-gray-100 mt-1">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">Google Connection Status</p>
                        <p class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#34A853] mt-1">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Linked (Active)
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">Account Created</p>
                        <p class="text-sm font-semibold text-[#202124] dark:text-gray-100 mt-1">{{ $user->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium uppercase tracking-wider">Last Updated</p>
                        <p class="text-sm font-semibold text-[#202124] dark:text-gray-100 mt-1">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Edit Profile Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-[#202124] dark:text-gray-100 mb-6">Edit Profile</h2>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                        @error('name')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preferred Display Name</label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $user->display_name) }}"
                               class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200"
                               placeholder="e.g. John">
                        @error('display_name')
                            <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Profile Photo Upload -->
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Picture (Optional)</label>
                    <input type="file" name="avatar" id="avatar" accept="image/*"
                           class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#4285F4]/10 file:text-[#4285F4] hover:file:bg-[#4285F4]/20 transition-all cursor-pointer">
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB.</p>
                    @error('avatar')
                        <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50 dark:border-gray-700">
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. Danger Zone Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-[#EA4335]/20 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-[#EA4335] mb-2">Danger Zone</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Once you delete your account, there is no going back. Please be certain.</p>

            <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-700">
                <div>
                    <p class="text-sm font-semibold text-[#202124] dark:text-gray-100">Delete Account</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Permanently delete your profile, settings, and all notes.</p>
                </div>
                <button type="button" @click="deleteModalOpen = true"
                        class="inline-flex items-center justify-center px-5 py-3 bg-[#EA4335]/10 text-[#EA4335] hover:bg-[#EA4335] hover:text-white text-sm font-medium rounded-xl transition-all duration-300">
                    Delete Account
                </button>
            </div>
        </div>

        <!-- Delete Account Confirmation Modal -->
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="deleteModalOpen = false">
                <div class="flex items-center gap-4 text-[#EA4335] mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#EA4335]/10 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100">Delete Account?</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                    Are you absolutely sure you want to permanently delete your account? This will erase all of your notes and settings. This action is irreversible.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition-all">
                        Cancel
                    </button>
                    <form action="{{ route('profile.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#EA4335] hover:bg-[#C53030] rounded-xl transition-all shadow-sm">
                            Permanently Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>