<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Manage Tags</h1>
    </x-slot>



    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ editModalOpen: false, editTagName: '', editTagUrl: '', deleteModalOpen: false, deleteTagUrl: '' }">
        
        <!-- 1. Create Tag Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 md:col-span-1 h-fit">
            <h2 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-4">Create New Tag</h2>
            <form action="{{ route('tags.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">Tag Name</label>
                    <input type="text" name="name" id="name" required max="50" placeholder="e.g. Work, Ideas" value="{{ old('name') }}"
                           class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200">
                    @error('name')
                        <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg transition-all duration-200">
                    Add Tag
                </button>
            </form>
        </div>

        <!-- 2. Tags List Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 md:col-span-2">
            <h2 class="text-base font-semibold text-[#202124] dark:text-gray-100 mb-4">Existing Tags</h2>

            @if ($tags->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($tags as $tag)
                        <div class="flex items-center justify-between py-3.5 group">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#4285F4]"></span>
                                <span class="text-sm font-semibold text-[#202124] dark:text-gray-100">{{ $tag->name }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $tag->notes()->count() }} {{ Str::plural('note', $tag->notes()->count()) }})</span>
                            </div>

                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 focus-within:opacity-100 transition-opacity duration-200">
                                <!-- Edit button -->
                                <button type="button" @click="editModalOpen = true; editTagName = '{{ $tag->name }}'; editTagUrl = '{{ route('tags.update', $tag) }}'"
                                        class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#FBBC05] hover:bg-[#FBBC05]/5 rounded-lg transition-all" title="Edit Tag">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <!-- Delete button -->
                                <button type="button" @click="deleteModalOpen = true; deleteTagUrl = '{{ route('tags.destroy', $tag) }}'"
                                        class="p-2 text-gray-400 dark:text-gray-500 hover:text-[#EA4335] hover:bg-[#EA4335]/5 rounded-lg transition-all" title="Delete Tag">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v13.5A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V14.4m-12-3h12" />
                    </svg>
                    <p class="text-sm">No tags created yet.</p>
                </div>
            @endif
        </div>

        <!-- 3. Edit Modal -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="editModalOpen = false">
                <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100 mb-4">Edit Tag Name</h3>
                <form :action="editTagUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <input type="text" name="name" x-model="editTagName" required max="50"
                               class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#4285F4] hover:bg-[#3367D6] rounded-xl shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 4. Delete Modal -->
        <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-md w-full p-6 shadow-xl border border-gray-100 dark:border-gray-700" @click.outside="deleteModalOpen = false">
                <div class="flex items-center gap-4 text-[#EA4335] mb-4">
                    <div class="w-12 h-12 rounded-full bg-[#EA4335]/10 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#202124] dark:text-gray-100">Delete Tag?</h3>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                    Are you sure you want to delete this tag? Notes linked to this tag will NOT be deleted, but they will be unlinked.
                </p>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl">
                        Cancel
                    </button>
                    <form :action="deleteTagUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#EA4335] hover:bg-[#C53030] rounded-xl shadow-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>