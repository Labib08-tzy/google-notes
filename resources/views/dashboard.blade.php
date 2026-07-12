<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Dashboard</h1>
    </x-slot>

    <!-- Welcome Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            @if (Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 rounded-full shadow-md ring-4 ring-white dark:ring-gray-700">
            @else
                <div class="w-16 h-16 rounded-full bg-[#4285F4] flex items-center justify-center text-white text-2xl font-semibold shadow-md ring-4 ring-white dark:ring-gray-700">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-semibold text-[#202124] dark:text-gray-100">
                    @php
                        $hour = now()->format('H');
                        if ($hour < 12) {
                            $greeting = 'Good Morning';
                        } elseif ($hour < 17) {
                            $greeting = 'Good Afternoon';
                        } else {
                            $greeting = 'Good Evening';
                        }
                    @endphp
                    {{ $greeting }}, {{ Auth::user()->display_name ?? Auth::user()->name }} 👋
                </h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Here's what's happening with your notes today.</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 sm:gap-6 mb-6">
        <!-- Total Notes -->
        <a href="{{ route('notes.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md hover:border-[#4285F4]/20 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Notes</p>
                    <p class="text-2xl font-bold text-[#202124] dark:text-gray-100 mt-1">{{ $totalNotes }}</p>
                </div>
                <div class="w-10 h-10 bg-[#4285F4]/10 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Pinned Notes -->
        <a href="{{ route('notes.index', ['filter' => 'pinned']) }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md hover:border-[#EA4335]/20 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Pinned</p>
                    <p class="text-2xl font-bold text-[#202124] dark:text-gray-100 mt-1">{{ $pinnedNotes }}</p>
                </div>
                <div class="w-10 h-10 bg-[#EA4335]/10 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-[#EA4335]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Favorites -->
        <a href="{{ route('notes.index', ['filter' => 'favorites']) }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md hover:border-[#FBBC05]/20 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Favorites</p>
                    <p class="text-2xl font-bold text-[#202124] dark:text-gray-100 mt-1">{{ $favoriteNotes }}</p>
                </div>
                <div class="w-10 h-10 bg-[#FBBC05]/10 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-[#FBBC05]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.198-.39.613-.39.812 0l2.84 5.79 6.386.928c.423.061.593.58.288.88l-4.62 4.502 1.09 6.364c.072.423-.37.745-.747.546L12 19.447l-5.717 3.007c-.378.199-.82-.123-.747-.546l1.09-6.364-4.62-4.502c-.305-.3-.135-.821.288-.88l6.386-.928 2.84-5.79z" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Archived -->
        <a href="{{ route('notes.archive-list') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md hover:border-[#34A853]/20 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Archived</p>
                    <p class="text-2xl font-bold text-[#202124] dark:text-gray-100 mt-1">{{ $archivedNotes }}</p>
                </div>
                <div class="w-10 h-10 bg-[#34A853]/10 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
            </div>
        </a>

        <!-- Trash -->
        <a href="{{ route('notes.trash-list') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Trash</p>
                    <p class="text-2xl font-bold text-[#202124] dark:text-gray-100 mt-1">{{ $trashNotes }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Search & Quick Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <!-- Search -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" placeholder="Search notes..." class="w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200" disabled>
        </div>

        <!-- Quick Actions -->
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('notes.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create Note
            </a>
            <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                View All Notes
            </a>
        </div>
    </div>

    <!-- Latest Notes Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-[#202124] dark:text-gray-100">Latest Notes</h3>
        </div>

        @if ($latestNotes->count() > 0)
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach ($latestNotes as $note)
                    <div class="px-6 py-4 hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors duration-200 group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-semibold text-[#202124] dark:text-gray-100 truncate group-hover:text-[#4285F4] transition-colors">
                                    <a href="{{ route('notes.show', $note) }}">{{ $note->title }}</a>
                                </h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ Str::limit($note->content, 120) }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                    {{ $note->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-[#34A853]/10 text-[#34A853]">
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="px-6 py-16 text-center">
                <div class="w-20 h-20 bg-[#4285F4]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#202124] dark:text-gray-100 mb-2">No notes yet</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 max-w-sm mx-auto">
                    Start capturing your ideas! Create your first note and keep your thoughts organized.
                </p>
                <a href="{{ route('notes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create Your First Note
                </a>
            </div>
        @endif
    </div>

</x-app-layout>