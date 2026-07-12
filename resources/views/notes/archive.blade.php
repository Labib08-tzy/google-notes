<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Archived Notes</h1>
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
        }
    }" class="space-y-6">

        <!-- Keyboard Shortcuts Listener -->
        <div x-init="
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('archive-search-input')?.focus();
                }
            });
        "></div>

        <!-- 1. Search Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 sm:p-5">
            <form action="{{ route('notes.archive-list') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search input -->
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="archive-search-input" value="{{ request('search') }}" placeholder="Search archived notes... (Ctrl + F)"
                           class="w-full pl-11 pr-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <select name="tag" onchange="this.form.submit()" class="px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                        <option value="">All Tags</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>Tag: {{ $tag->name }}</option>
                        @endforeach
                    </select>

                    @if (request('search') || request('tag'))
                        <a href="{{ route('notes.archive-list') }}" class="text-xs text-[#4285F4] hover:underline">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 2. Bulk Action Toolbar -->
        <div x-show="selectedNotes.length > 0" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-[#34A853]/5 border border-[#34A853]/10 dark:border-[#34A853]/20 rounded-2xl">
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-[#34A853]" x-text="selectedNotes.length + ' notes selected'"></span>
                <button type="button" @click="toggleAll" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 underline">
                    <span x-text="selectedNotes.length === allNoteIds.length ? 'Deselect All' : 'Select All'"></span>
                </button>
            </div>

            <!-- Bulk actions form -->
            <form action="{{ route('notes.bulk') }}" method="POST" class="flex flex-wrap items-center gap-2">
                @csrf
                <template x-for="id in selectedNotes">
                    <input type="hidden" name="note_ids[]" :value="id">
                </template>
                <input type="hidden" name="action" id="bulk-action-input">

                <button type="button" @click="document.getElementById('bulk-action-input').value = 'restore'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Unarchive / Restore
                </button>
                <button type="button" @click="document.getElementById('bulk-action-input').value = 'delete'; $el.form.submit()" class="px-3 py-1.5 bg-[#EA4335]/10 text-[#EA4335] rounded-lg text-xs font-semibold hover:bg-[#EA4335] hover:text-white">
                    Move to Trash
                </button>
            </form>
        </div>

        <!-- 3. Notes Grid / List -->
        @if ($notes->count() > 0)
            <div class="{{ $settings->dashboard_layout === 'list' ? 'grid grid-cols-1 gap-4' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' }}">
                @foreach ($notes as $note)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 flex {{ $settings->dashboard_layout === 'list' ? 'flex-col sm:flex-row sm:items-center sm:justify-between gap-6 h-auto py-4' : 'flex-col justify-between h-72' }} group relative">
                        
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <!-- Checkbox for Bulk Actions -->
                            <input type="checkbox" :value="{{ $note->id }}" x-model="selectedNotes"
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-[#4285F4] focus:ring-[#4285F4] cursor-pointer">
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-2 truncate group-hover:text-[#4285F4] transition-colors">
                                    <a href="{{ route('notes.show', $note) }}">{{ $note->title }}</a>
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 leading-relaxed {{ $settings->dashboard_layout === 'list' ? 'line-clamp-1 sm:line-clamp-2' : 'line-clamp-3' }}">
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
                                <p>Archived: {{ $note->archived_at ? $note->archived_at->format('M d, Y') : $note->updated_at->format('M d, Y') }}</p>
                            </div>

                            <!-- Card Action Buttons -->
                            <div class="flex items-center gap-1">
                                <!-- Unarchive Button -->
                                <form action="{{ route('notes.archive', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#4285F4] hover:bg-[#4285F4]/5 rounded-lg transition-all" title="Unarchive / Restore">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Delete Button (Move to Trash) -->
                                <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#EA4335] hover:bg-[#EA4335]/5 rounded-lg transition-all" title="Move to Trash">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="mt-8">
                {{ $notes->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-16 text-center">
                <div class="w-20 h-20 bg-[#34A853]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#34A853]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#202124] dark:text-gray-100 mb-2">No archived notes</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 max-w-sm mx-auto">
                    Keep your active space clean by archiving notes that you aren't currently working on.
                </p>
                <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                    Go to Active Notes
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
