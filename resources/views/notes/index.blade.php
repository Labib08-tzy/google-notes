<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">My Notes</h1>
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
                    document.getElementById('notes-search-input')?.focus();
                }
                if (e.key === 'Delete' && $data.selectedNotes.length > 0) {
                    document.getElementById('bulk-action-input').value = 'delete';
                    document.getElementById('bulk-action-form').submit();
                }
            });
        "></div>

        <!-- 1. Search & Filter Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 sm:p-5">
            <form action="{{ route('notes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Search input -->
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="notes-search-input" value="{{ request('search') }}" placeholder="Search title or content... (Ctrl + F)"
                           class="w-full pl-11 pr-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                </div>

                <!-- Advanced Filters -->
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <!-- Categories -->
                    <select name="filter" onchange="this.form.submit()" class="px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                        <option value="">All Notes</option>
                        <option value="favorites" {{ request('filter') === 'favorites' ? 'selected' : '' }}>Favorites</option>
                        <option value="pinned" {{ request('filter') === 'pinned' ? 'selected' : '' }}>Pinned</option>
                        <option value="recently_created" {{ request('filter') === 'recently_created' ? 'selected' : '' }}>Recently Created</option>
                        <option value="recently_updated" {{ request('filter') === 'recently_updated' ? 'selected' : '' }}>Recently Updated</option>
                    </select>

                    <!-- Tags -->
                    <select name="tag" onchange="this.form.submit()" class="px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                        <option value="">All Tags</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>Tag: {{ $tag->name }}</option>
                        @endforeach
                    </select>

                    <!-- Reset Link -->
                    @if (request('search') || request('filter') || request('tag'))
                        <a href="{{ route('notes.index') }}" class="text-xs text-[#4285F4] hover:underline">Clear Filters</a>
                    @endif

                    <a href="{{ route('notes.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg transition-all ml-auto md:ml-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Create Note
                    </a>
                </div>
            </form>
        </div>

        <!-- 2. Bulk Action Toolbar -->
        <div x-show="selectedNotes.length > 0" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-[#4285F4]/5 border border-[#4285F4]/10 dark:border-[#4285F4]/20 rounded-2xl">
            
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-[#4285F4]" x-text="selectedNotes.length + ' notes selected'"></span>
                <button type="button" @click="toggleAll" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 underline">
                    <span x-text="selectedNotes.length === allNoteIds.length ? 'Deselect All' : 'Select All'"></span>
                </button>
            </div>

            <!-- Bulk actions form -->
            <form id="bulk-action-form" action="{{ route('notes.bulk') }}" method="POST" class="flex flex-wrap items-center gap-2">
                @csrf
                <template x-for="id in selectedNotes">
                    <input type="hidden" name="note_ids[]" :value="id">
                </template>
                <input type="hidden" name="action" id="bulk-action-input">

                <button type="button" @click="document.getElementById('bulk-action-input').value = 'favorite'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Favorite
                </button>
                <button type="button" @click="document.getElementById('bulk-action-input').value = 'unfavorite'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Unfavorite
                </button>
                <button type="button" @click="document.getElementById('bulk-action-input').value = 'pin'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Pin
                </button>
                <button type="button" @click="document.getElementById('bulk-action-input').value = 'unpin'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Unpin
                </button>
                <button type="button" @click="document.getElementById('bulk-action-input').value = 'archive'; $el.form.submit()" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Archive
                </button>
                <button type="button" id="bulk-delete-btn" @click="document.getElementById('bulk-action-input').value = 'delete'; $el.form.submit()" class="px-3 py-1.5 bg-[#EA4335]/10 text-[#EA4335] border border-transparent rounded-lg text-xs font-semibold hover:bg-[#EA4335] hover:text-white">
                    Move to Trash (Del)
                </button>
            </form>
        </div>

        <!-- 3. Notes Grid / List -->
        @if ($notes->count() > 0)
            <div class="{{ $settings->dashboard_layout === 'list' ? 'grid grid-cols-1 gap-4' : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' }}" x-data="{ deleteModalOpen: false, deleteUrl: '' }">
                @foreach ($notes as $note)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 hover:shadow-md hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-300 flex {{ $settings->dashboard_layout === 'list' ? 'flex-col sm:flex-row sm:items-center sm:justify-between gap-6 h-auto py-4' : 'flex-col justify-between h-72' }} group relative">
                        
                        <!-- Pinned Indicator / Ribbons -->
                        @if($note->is_pinned)
                            <span class="absolute top-0 right-12 w-6 h-6 bg-[#EA4335] text-white flex items-center justify-center rounded-b-md shadow-sm" title="Pinned Note">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727.121l5 3.5a1 1 0 01.382.909l-.5 8a1 1 0 01-.849.932l-5 1a1 1 0 01-1.002-.54L6.162 14.3a1 1 0 01.127-1.128l7-7a1 1 0 00-1.414-1.414l-7 7a1 1 0 01-1.128.127L3.06 10.02a1 1 0 01-.54-1.002l1-5a1 1 0 01.932-.849l8-.5a1 1 0 01.791.361z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif

                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <!-- Checkbox for Bulk Actions -->
                            <input type="checkbox" :value="{{ $note->id }}" x-model="selectedNotes"
                                   class="mt-1 rounded border-gray-300 dark:border-gray-600 text-[#4285F4] focus:ring-[#4285F4] cursor-pointer">
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="text-base font-semibold text-[#202124] dark:text-gray-100 line-clamp-1 group-hover:text-[#4285F4] transition-colors">
                                        <a href="{{ route('notes.show', $note) }}">{{ $note->title }}</a>
                                    </h3>
                                </div>
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
                            <div class="text-xs text-gray-400 dark:text-gray-505 {{ $settings->dashboard_layout === 'list' ? 'text-left sm:text-right' : '' }}">
                                <p>Created: {{ $note->created_at->format('M d, Y') }}</p>
                                @if($note->updated_at > $note->created_at)
                                    <p class="mt-0.5">Updated: {{ $note->updated_at->format('M d, Y') }}</p>
                                @endif
                            </div>

                            <!-- Card Action Buttons -->
                            <div class="flex items-center gap-1">
                                <!-- Pin Button -->
                                <form action="{{ route('notes.pin', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 {{ $note->is_pinned ? 'text-[#EA4335] bg-[#EA4335]/5' : 'text-gray-400 dark:text-gray-505 hover:text-[#EA4335] hover:bg-[#EA4335]/5' }} rounded-lg transition-all" title="{{ $note->is_pinned ? 'Unpin Note' : 'Pin Note' }}">
                                        <svg class="w-4 h-4" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Favorite Button -->
                                <form action="{{ route('notes.favorite', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 {{ $note->is_favorite ? 'text-[#FBBC05] bg-[#FBBC05]/5' : 'text-gray-400 dark:text-gray-505 hover:text-[#FBBC05] hover:bg-[#FBBC05]/5' }} rounded-lg transition-all" title="{{ $note->is_favorite ? 'Unfavorite' : 'Favorite' }}">
                                        <svg class="w-4 h-4" fill="{{ $note->is_favorite ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499c.198-.39.613-.39.812 0l2.84 5.79 6.386.928c.423.061.593.58.288.88l-4.62 4.502 1.09 6.364c.072.423-.37.745-.747.546L12 19.447l-5.717 3.007c-.378.199-.82-.123-.747-.546l1.09-6.364-4.62-4.502c-.305-.3-.135-.821.288-.88l6.386-.928 2.84-5.79z" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Archive Button -->
                                <form action="{{ route('notes.archive', $note) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="p-2 text-gray-400 dark:text-gray-505 hover:text-[#34A853] hover:bg-[#34A853]/5 rounded-lg transition-all" title="Archive Note">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                    </button>
                                </form>

                                <!-- Edit Button -->
                                <a href="{{ route('notes.edit', $note) }}" class="p-2 text-gray-400 dark:text-gray-505 hover:text-[#FBBC05] hover:bg-[#FBBC05]/5 rounded-lg transition-all" title="Edit Note">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </a>

                                <!-- Delete Button (Move to Trash) -->
                                <button @click="deleteModalOpen = true; deleteUrl = '{{ route('notes.destroy', $note) }}'" class="p-2 text-gray-400 dark:text-gray-505 hover:text-[#EA4335] hover:bg-[#EA4335]/5 rounded-lg transition-all" title="Move to Trash">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Delete Confirmation Modal (Native Tailwind/Alpine Implementation) -->
                <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="deleteModalOpen = false">
                        <div class="flex items-center gap-4 text-[#EA4335] mb-4">
                            <div class="w-12 h-12 rounded-full bg-[#EA4335]/10 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100">Move Note to Trash?</h3>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                            Are you sure you want to move this note to the Trash? You can restore it later.
                        </p>
                        <div class="flex items-center justify-end gap-3">
                            <button @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition-all">
                                Cancel
                            </button>
                            <form :action="deleteUrl" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#EA4335] hover:bg-[#C53030] rounded-xl transition-all shadow-sm">
                                    Move to Trash
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
                <div class="w-20 h-20 bg-[#4285F4]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-[#4285F4]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-[#202124] dark:text-gray-100 mb-2">No matching notes found</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-6 max-w-sm mx-auto">
                    Try adjusting your search criteria or clear your current filters to find your notes.
                </p>
                <a href="{{ route('notes.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                    Clear Filters & Search
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
