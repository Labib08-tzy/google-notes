<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Trash Bin</h1>
    </x-slot>

    <div x-data="{ 
        selectedNotes: [], 
        allNoteIds: {{ json_encode($notes->pluck('id')) }},
        toggleAll() {
            if (this.selectedNotes.length === this.allNoteIds.length) {
                this.selectedNotes = [];
            } else {
                this.selectedNotes = [...this.allNoteIds];
            }
        },
        bulkConfirmOpen: false
    }" class="space-y-6">

        <!-- Keyboard Shortcuts Listener -->
        <div x-init="
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('trash-search-input')?.focus();
                }
            });
        "></div>

        <!-- 1. Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 sm:p-5">
            <form action="{{ route('notes.trash-list') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search input -->
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="trash-search-input" value="{{ request('search') }}" placeholder="Search trashed notes... (Ctrl + F)"
                           class="w-full pl-11 pr-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                </div>

                @if (request('search'))
                    <a href="{{ route('notes.trash-list') }}" class="text-xs text-[#4285F4] hover:underline">Clear Search</a>
                @endif
            </form>
        </div>

        <!-- 2. Bulk Action Toolbar -->
        <div x-show="selectedNotes.length > 0" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 rounded-2xl">
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-[#EA4335]" x-text="selectedNotes.length + ' notes selected'"></span>
                <button type="button" @click="toggleAll" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 underline">
                    <span x-text="selectedNotes.length === allNoteIds.length ? 'Deselect All' : 'Select All'"></span>
                </button>
            </div>

            <!-- Bulk actions form -->
            <form id="bulk-trash-form" action="{{ route('notes.bulk') }}" method="POST" class="flex flex-wrap items-center gap-2">
                @csrf
                <template x-for="id in selectedNotes">
                    <input type="hidden" name="note_ids[]" :value="id">
                </template>
                <input type="hidden" name="action" id="bulk-trash-action-input">

                <button type="button" @click="document.getElementById('bulk-trash-action-input').value = 'restore'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Restore Selected
                </button>
                <button type="button" @click="bulkConfirmOpen = true" class="px-3 py-1.5 bg-[#EA4335] text-white rounded-lg text-xs font-semibold hover:bg-[#C53030]">
                    Permanently Delete Selected
                </button>
            </form>
        </div>

        <!-- Bulk Delete Confirmation Modal -->
        <div x-show="bulkConfirmOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="bulkConfirmOpen = false">
                <div class="flex items-center gap-4 text-[#EA4335] mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#EA4335]/10 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100">Permanently Delete Selected Notes?</h3>
                </div>
                <p class="text-sm text-gray-505 dark:text-gray-400 mb-6 leading-relaxed">
                    This will permanently delete the selected notes. This action is destructive and cannot be undone.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="bulkConfirmOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl">
                        Cancel
                    </button>
                    <button type="button" @click="document.getElementById('bulk-trash-action-input').value = 'force_delete'; document.getElementById('bulk-trash-form').submit();"
                            class="px-5 py-2 text-sm font-medium text-white bg-[#EA4335] hover:bg-[#C53030] rounded-xl shadow-sm">
                        Confirm Permanent Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. Notes Grid / List -->
        @if ($notes->count() > 0)
            <div class="{{ $settings->dashboard_layout === 'list' ? 'grid grid-cols-1 gap-4' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' }}" x-data="{ forceDeleteModalOpen: false, forceDeleteUrl: '' }">
                @foreach ($notes as $note)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 flex {{ $settings->dashboard_layout === 'list' ? 'flex-col sm:flex-row sm:items-center sm:justify-between gap-6 h-auto py-4' : 'flex-col justify-between h-72' }} group relative">
                        
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <!-- Checkbox for Bulk Actions -->
                            <input type="checkbox" :value="{{ $note->id }}" x-model="selectedNotes"
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-[#4285F4] focus:ring-[#4285F4] cursor-pointer">
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-2 truncate">
                                    {{ $note->title }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-405 mb-2 leading-relaxed {{ $settings->dashboard_layout === 'list' ? 'line-clamp-1 sm:line-clamp-2' : 'line-clamp-3' }}">
                                    {{ $note->content }}
                                </p>

                                <!-- Tags rendering -->
                                @if($note->tags->count() > 0)
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @foreach($note->tags as $tag)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[#4285F4]/10 text-[#4285F4]">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="{{ $settings->dashboard_layout === 'list' ? 'flex items-center gap-6 shrink-0' : 'border-t border-gray-50 dark:border-gray-700 pt-4 flex items-center justify-between' }}">
                            <div class="text-xs text-gray-400 dark:text-gray-500 {{ $settings->dashboard_layout === 'list' ? 'text-left sm:text-right' : '' }}">
                                <p>Deleted: {{ $note->deleted_at->format('M d, Y') }}</p>
                            </div>

                            <!-- Card Action Buttons -->
                            <div class="flex items-center gap-1">
                                <!-- Restore Button -->
                                <form action="{{ route('notes.restore', $note->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#4285F4] hover:bg-[#4285F4]/5 rounded-lg transition-all" title="Restore Note">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Permanent Delete Button -->
                                <button type="button" @click="forceDeleteModalOpen = true; forceDeleteUrl = '{{ route('notes.force-delete', $note->id) }}'"
                                        class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#EA4335] hover:bg-[#EA4335]/5 rounded-lg transition-all" title="Delete Permanently">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Force Delete Modal -->
                <div x-show="forceDeleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="forceDeleteModalOpen = false">
                        <div class="flex items-center gap-4 text-[#EA4335] mb-4">
                            <div class="w-12 h-12 rounded-full bg-[#EA4335]/10 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100">Permanently Delete Note?</h3>
                        </div>
                        <p class="text-sm text-gray-505 dark:text-gray-400 mb-6 leading-relaxed">
                            This note will be permanently deleted and cannot be restored. Are you sure you want to proceed?
                        </p>
                        <div class="flex items-center justify-end gap-3">
                            <button @click="forceDeleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition-all">
                                Cancel
                            </button>
                            <form :action="forceDeleteUrl" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#EA4335] hover:bg-[#C53030] rounded-xl transition-all shadow-sm">
                                    Delete Permanently
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pagination Links -->
            <div class="mt-8">
                {{ $notes->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-505" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#202124] dark:text-gray-100 mb-2">Trash is empty</h3>
                <p class="text-gray-500 dark:text-gray-404 text-sm mb-6 max-w-sm mx-auto">
                    Notes you delete will appear here. You can choose to restore them or delete them permanently.
                </p>
                <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                    Go to Dashboard
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
