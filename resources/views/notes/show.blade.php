<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Note Detail</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto" x-data="{ deleteModalOpen: false }">
        <!-- Back Button -->
        <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-[#4285F4] dark:hover:text-blue-400 mb-6 transition-all duration-200 group">
            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Notes
        </a>

        <!-- Note Detail Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
                <!-- Header -->
                <div class="flex items-start justify-between gap-6 border-b border-gray-50 dark:border-gray-700 pb-6 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-[#202124] dark:text-gray-100 leading-tight">
                            {{ $note->title }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-xs text-gray-400 dark:text-gray-500">
                            <span>Created: {{ $note->created_at->format('M d, Y h:i A') }}</span>
                            @if($note->updated_at > $note->created_at)
                                <span class="hidden sm:inline">•</span>
                                <span>Updated: {{ $note->updated_at->format('M d, Y h:i A') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Detail Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('notes.edit', $note) }}" class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#FBBC05] hover:bg-[#FBBC05]/5 rounded-lg transition-all" title="Edit Note">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                        </a>
                        <button @click="deleteModalOpen = true" class="p-2 text-gray-400 dark:text-gray-505 hover:text-[#EA4335] hover:bg-[#EA4335]/5 rounded-lg transition-all" title="Delete Note">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="text-[#202124] dark:text-gray-100 text-sm sm:text-base leading-relaxed whitespace-pre-wrap">
                    {{ $note->content }}
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal (Native Tailwind/Alpine Implementation) -->
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="deleteModalOpen = false">
                <div class="flex items-center gap-4 text-[#EA4335] mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#EA4335]/10 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100">Delete Note?</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                    Are you sure you want to delete this note? This action cannot be undone.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition-all">
                        Cancel
                    </button>
                    <form action="{{ route('notes.destroy', $note) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#EA4335] hover:bg-[#C53030] rounded-xl transition-all shadow-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
