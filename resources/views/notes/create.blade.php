<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-[#202124] dark:text-gray-100">Create Note</h1>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6 sm:p-8" x-data="{
            loading: false,
            aiOutput: '',
            aiError: '',
            currentAction: '',
            translateOpen: false,
            async callAi(action, extra = {}) {
                const textarea = document.getElementById('content');
                const content = textarea.value.trim();
                if (!content) {
                    this.aiError = 'Please write some content first before using AI features.';
                    return;
                }
                
                this.loading = true;
                this.aiError = '';
                this.aiOutput = '';
                this.currentAction = action;

                try {
                    const response = await fetch(`/ai/${action}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            content: content,
                            note_id: null,
                            ...extra
                        })
                    });

                    const data = await response.json();
                    if (response.status === 429) {
                        this.aiError = data.error || 'Rate limit exceeded. Please try again later.';
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.aiError, type: 'warning' } }));
                    } else if (!response.ok) {
                        throw new Error(data.error || 'Server error');
                    } else if (data.error) {
                        this.aiError = data.error;
                    } else {
                        this.aiOutput = data.result;
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'AI processing complete', type: 'success' } }));
                    }
                } catch (err) {
                    this.aiError = err.message || 'Failed to generate AI response. Please try again.';
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: this.aiError, type: 'error' } }));
                } finally {
                    this.loading = false;
                }
            },
            insertContent() {
                const textarea = document.getElementById('content');
                textarea.value = textarea.value + '\n\n' + this.aiOutput;
                textarea.dispatchEvent(new Event('input'));
                this.aiOutput = '';
            },
            replaceContent() {
                const textarea = document.getElementById('content');
                textarea.value = this.aiOutput;
                textarea.dispatchEvent(new Event('input'));
                this.aiOutput = '';
            },
            updateTitle() {
                const titleInput = document.getElementById('title');
                titleInput.value = this.aiOutput;
                titleInput.dispatchEvent(new Event('input'));
                this.aiOutput = '';
            },
            copyToClipboard() {
                navigator.clipboard.writeText(this.aiOutput);
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Copied to clipboard', type: 'success' } }));
            }
        }">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-[#202124] dark:text-gray-100">Write a new note</h2>
                <!-- Template Button -->
                <div class="relative" x-data="{ templateOpen: false }">
                    <button type="button" @click="templateOpen = !templateOpen"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/40 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                        </svg>
                        Templates
                    </button>
                    <div x-show="templateOpen" @click.outside="templateOpen = false" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-amber-100 dark:border-gray-700 py-2 z-20">
                        <p class="px-4 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Choose Template</p>
                        <button type="button" @click="
                            document.getElementById('title').value = 'Meeting Notes — ' + new Date().toLocaleDateString();
                            document.getElementById('content').value = '📋 MEETING NOTES\n\nDate: ' + new Date().toLocaleDateString() + '\nAttendees:\n- \n\nAgenda:\n1. \n\nDiscussion Points:\n- \n\nDecisions Made:\n- \n\nAction Items:\n- [ ] \n\nNext Meeting: ';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">📋</span> Meeting Notes
                        </button>
                        <button type="button" @click="
                            document.getElementById('title').value = 'To-Do List';
                            document.getElementById('content').value = '✅ TO-DO LIST\n\n[ ] Task 1\n[ ] Task 2\n[ ] Task 3\n[ ] Task 4\n[ ] Task 5\n\n📌 Priority:\n- High: \n- Medium: \n- Low: ';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">✅</span> To-Do List
                        </button>
                        <button type="button" @click="
                            document.getElementById('title').value = 'Daily Journal — ' + new Date().toLocaleDateString();
                            document.getElementById('content').value = '📓 DAILY JOURNAL\n\nDate: ' + new Date().toLocaleDateString() + '\n\nHow I feel today:\n\nWhat I did today:\n1. \n2. \n3. \n\nWhat I am grateful for:\n- \n\nGoals for tomorrow:\n- \n\nNotes:';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">📓</span> Daily Journal
                        </button>
                        <button type="button" @click="
                            document.getElementById('title').value = 'Project Plan';
                            document.getElementById('content').value = '🚀 PROJECT PLAN\n\nProject Name:\nObjective:\nTimeline: \n\nPhases:\n1. Phase 1 — Planning\n   - \n2. Phase 2 — Execution\n   - \n3. Phase 3 — Review\n   - \n\nResources Needed:\n- \n\nRisks:\n- \n\nSuccess Criteria:\n- ';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">🚀</span> Project Plan
                        </button>
                        <button type="button" @click="
                            document.getElementById('title').value = 'Brainstorm — Ideas';
                            document.getElementById('content').value = '💡 BRAINSTORM SESSION\n\nTopic:\n\nIdeas (no filter, just write!):\n- \n- \n- \n- \n- \n\nTop 3 Ideas:\n1. \n2. \n3. \n\nNext Steps:\n- ';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">💡</span> Idea Brainstorm
                        </button>
                        <button type="button" @click="
                            document.getElementById('title').value = 'Weekly Review';
                            document.getElementById('content').value = '📅 WEEKLY REVIEW\n\nWeek of: \n\nWins this week:\n- \n\nChallenges faced:\n- \n\nLessons learned:\n- \n\nGoals for next week:\n1. \n2. \n3. \n\nRating this week (1-10):';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">📅</span> Weekly Review
                        </button>
                        <button type="button" @click="
                            document.getElementById('title').value = 'Study Notes';
                            document.getElementById('content').value = '📚 STUDY NOTES\n\nSubject:\nTopic:\nDate: ' + new Date().toLocaleDateString() + '\n\nKey Concepts:\n1. \n2. \n3. \n\nImportant Definitions:\n- Term: \n\nFormulas / Methods:\n- \n\nQuestions to Review:\n- \n\nSummary:';
                            templateOpen = false;
                        " class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:text-amber-700 transition-colors">
                            <span class="text-lg">📚</span> Study Notes
                        </button>
                    </div>
                </div>
            </div>

            <form action="{{ route('notes.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Title Input -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200" 
                           placeholder="Enter note title..." required autofocus>
                    @error('title')
                        <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                    @if($tags->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#F8F9FA] dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-xl text-xs font-semibold text-[#202124] dark:text-gray-100 cursor-pointer transition-colors">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 text-[#4285F4] focus:ring-[#4285F4]">
                                    {{ $tag->name }}
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            No tags created yet. <a href="{{ route('tags.index') }}" class="text-[#4285F4] hover:underline">Create tags here</a> to organize your notes.
                        </p>
                    @endif
                </div>

                <!-- Content Input -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                    <textarea name="content" id="content" rows="8" 
                              class="w-full px-4 py-3 bg-[#F8F9FA] dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-[#202124] dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#4285F4]/20 focus:border-[#4285F4] transition-all duration-200 resize-y" 
                              placeholder="Write your note content here..." required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-xs text-[#EA4335]">{{ $message }}</p>
                    @enderror

                    <!-- AI Assistant Toolbar -->
                    <div class="mt-4 p-4 bg-[#F8F9FA] dark:bg-gray-700 rounded-xl border border-gray-100 dark:border-gray-600">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider flex items-center gap-1.5 mr-2">
                                <svg class="w-4 h-4 text-[#4285F4] animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 18.5 7.854a1 1 0 01.55 1.706l-3.146 3.068.742 4.338a1 1 0 01-1.451 1.054L11 16l-3.957 2.08a1 1 0 01-1.45-1.054l.742-4.338L3.19 9.56a1 1 0 01.55-1.706l4.354-.655 1.933-4.456A1 1 0 0112 2z" clip-rule="evenodd" />
                                </svg>
                                AI Assistant
                            </span>

                            <button type="button" @click="callAi('summarize')" :disabled="loading" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#4285F4] hover:border-[#4285F4]/30 hover:bg-[#4285F4]/5 dark:hover:bg-[#4285F4]/10 disabled:opacity-50 transition-all duration-200 flex items-center gap-1">
                                Summarize
                            </button>
                            
                            <button type="button" @click="callAi('improve')" :disabled="loading" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#34A853] hover:border-[#34A853]/30 hover:bg-[#34A853]/5 dark:hover:bg-[#34A853]/10 disabled:opacity-50 transition-all duration-200 flex items-center gap-1">
                                Improve Writing
                            </button>
                            
                            <button type="button" @click="callAi('continue')" :disabled="loading" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#FBBC05] hover:border-[#FBBC05]/30 hover:bg-[#FBBC05]/5 dark:hover:bg-[#FBBC05]/10 disabled:opacity-50 transition-all duration-200 flex items-center gap-1">
                                Continue Writing
                            </button>
                            
                            <button type="button" @click="callAi('title')" :disabled="loading" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#EA4335] hover:border-[#EA4335]/30 hover:bg-[#EA4335]/5 dark:hover:bg-[#EA4335]/10 disabled:opacity-50 transition-all duration-200 flex items-center gap-1">
                                Generate Title
                            </button>

                            <button type="button" @click="callAi('explain')" :disabled="loading" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#4285F4] hover:border-[#4285F4]/30 hover:bg-[#4285F4]/5 dark:hover:bg-[#4285F4]/10 disabled:opacity-50 transition-all duration-200 flex items-center gap-1">
                                Explain
                            </button>

                            <div class="relative">
                                <button type="button" @click="translateOpen = !translateOpen" :disabled="loading" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:text-[#34A853] hover:border-[#34A853]/30 hover:bg-[#34A853]/5 dark:hover:bg-[#34A853]/10 disabled:opacity-50 transition-all duration-200 flex items-center gap-1">
                                    Translate
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="translateOpen" @click.outside="translateOpen = false" class="absolute left-0 mt-1 w-52 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 py-1 z-10" x-cloak>
                                    <p class="px-3 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Select Language</p>
                                    <button type="button" @click="callAi('translate', { target_language: 'id' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇮🇩 Indonesian</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'en' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇺🇸 English</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'es' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇪🇸 Spanish</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'fr' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇫🇷 French</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'de' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇩🇪 German</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'ja' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇯🇵 Japanese</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'ko' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇰🇷 Korean</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'zh' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇨🇳 Chinese</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'ar' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇸🇦 Arabic</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'pt' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇧🇷 Portuguese</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'ru' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇷🇺 Russian</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'hi' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇮🇳 Hindi</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'it' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇮🇹 Italian</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'nl' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇳🇱 Dutch</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'tr' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇹🇷 Turkish</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'vi' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇻🇳 Vietnamese</button>
                                    <button type="button" @click="callAi('translate', { target_language: 'th' }); translateOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#34A853] transition-colors">🇹🇭 Thai</button>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loading" class="mt-4 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400" x-cloak>
                            <svg class="animate-spin h-4 w-4 text-[#4285F4]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Gemini is thinking...</span>
                        </div>

                        <!-- Error State -->
                        <div x-show="aiError" class="mt-4 p-3 bg-red-50 border border-red-100 text-[#EA4335] rounded-lg text-xs flex items-start gap-2" x-cloak>
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="aiError"></span>
                        </div>

                        <!-- AI Output Box -->
                        <div x-show="aiOutput" class="mt-4 p-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-sm" x-cloak>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-505 mb-2 uppercase tracking-wider">AI Generated Result</p>
                            <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap bg-[#F8F9FA] dark:bg-gray-700 p-3 rounded-lg border border-gray-50 dark:border-gray-600 mb-3 max-h-60 overflow-y-auto" x-text="aiOutput"></div>
                            <div class="flex flex-wrap gap-2 justify-end">
                                <button type="button" @click="copyToClipboard" class="px-3 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Copy
                                </button>
                                <button type="button" @click="insertContent" class="px-3 py-1.5 bg-[#4285F4] text-white rounded-lg text-xs font-medium hover:bg-[#3367D6] transition-colors">
                                    Insert/Append
                                </button>
                                <button type="button" @click="replaceContent" class="px-3 py-1.5 bg-[#34A853] text-white rounded-lg text-xs font-medium hover:bg-[#2C8E46] transition-colors">
                                    Replace All
                                </button>
                                <button type="button" x-show="currentAction === 'title'" @click="updateTitle" class="px-3 py-1.5 bg-[#FBBC05] text-white rounded-lg text-xs font-medium hover:bg-[#E5A904] transition-colors">
                                    Use as Title
                                </button>
                                <button type="button" @click="aiOutput = ''" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-50 dark:border-gray-700">
                    <a href="{{ route('notes.index') }}" 
                       class="inline-flex items-center justify-center px-5 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 border border-transparent">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center justify-center px-6 py-3 bg-[#4285F4] text-white text-sm font-medium rounded-xl hover:bg-[#3367D6] hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 transform hover:-translate-y-0.5">
                        Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
