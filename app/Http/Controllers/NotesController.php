<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NotesController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index(): View
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();
        $tags = $user->tags()->orderBy('name', 'asc')->get();

        $query = $user->notes()->where('is_archived', false)->with('tags');

        // Apply Search
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply Tag Filter
        if ($tagId = request('tag')) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        // Apply Advanced Filters
        if ($filter = request('filter')) {
            switch ($filter) {
                case 'favorites':
                    $query->where('is_favorite', true);
                    break;
                case 'pinned':
                    $query->where('is_pinned', true);
                    break;
                case 'recently_created':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'recently_updated':
                    $query->orderBy('updated_at', 'desc');
                    break;
            }
        }

        // Pinned notes always appear before regular notes
        $query->orderBy('is_pinned', 'desc');

        // Apply Sorting Preference (if filter hasn't already defined sorting)
        if (!in_array(request('filter'), ['recently_created', 'recently_updated'])) {
            switch ($settings->default_sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }

        $notes = $query->paginate($settings->notes_per_page)->withQueryString();

        return view('notes.index', compact('notes', 'settings', 'tags'));
    }

    /**
     * Show the form for creating a new note.
     */
    public function create(): View
    {
        $tags = Auth::user()->tags()->orderBy('name', 'asc')->get();
        return view('notes.create', compact('tags'));
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);

        $note = Auth::user()->notes()->create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_archived' => false,
        ]);

        // Secure tag sync
        if ($request->has('tags')) {
            $userTagIds = Auth::user()->tags()
                ->whereIn('id', $request->input('tags', []))
                ->pluck('id')
                ->toArray();
            $note->tags()->sync($userTagIds);
        }

        $this->invalidateStatsCache();

        return redirect()->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    /**
     * Display the specified note.
     */
    public function show(Note $note): View
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('notes.show', compact('note'));
    }

    /**
     * Show the form for editing the specified note.
     */
    public function edit(Note $note): View
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $tags = Auth::user()->tags()->orderBy('name', 'asc')->get();
        return view('notes.edit', compact('note', 'tags'));
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ]);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        // Secure tag sync
        $userTagIds = Auth::user()->tags()
            ->whereIn('id', $request->input('tags', []))
            ->pluck('id')
            ->toArray();
        $note->tags()->sync($userTagIds);

        $this->invalidateStatsCache();

        return redirect()->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Autosave a note via AJAX.
     */
    public function autosave(Request $request, Note $note): \Illuminate\Http\JsonResponse
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        $this->invalidateStatsCache();

        Log::info("Note {$note->id} autosaved for user " . Auth::id());

        return response()->json(['success' => true]);
    }

    /**
     * Move note to trash.
     */
    public function destroy(Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $note->delete();

        $this->invalidateStatsCache();

        return redirect()->route('notes.index')
            ->with('success', 'Note moved to Trash.');
    }

    /**
     * Toggle Favorite status.
     */
    public function toggleFavorite(Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $note->update(['is_favorite' => !$note->is_favorite]);

        $this->invalidateStatsCache();

        return back()->with('success', $note->is_favorite ? 'Added to favorites.' : 'Removed from favorites.');
    }

    /**
     * Toggle Pin status.
     */
    public function togglePin(Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $note->update(['is_pinned' => !$note->is_pinned]);

        $this->invalidateStatsCache();

        return back()->with('success', $note->is_pinned ? 'Note pinned to top.' : 'Note unpinned.');
    }

    /**
     * Toggle Archive status.
     */
    public function toggleArchive(Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $isArchived = !$note->is_archived;
        $note->update([
            'is_archived' => $isArchived,
            'archived_at' => $isArchived ? now() : null,
        ]);

        $this->invalidateStatsCache();

        return back()->with('success', $isArchived ? 'Note archived.' : 'Note restored from archive.');
    }

    /**
     * Restore from Trash.
     */
    public function restore(int $id): RedirectResponse
    {
        $note = Auth::user()->notes()->onlyTrashed()->findOrFail($id);
        $note->restore();

        $this->invalidateStatsCache();

        return redirect()->route('notes.trash-list')
            ->with('success', 'Note restored successfully.');
    }

    /**
     * Permanently Delete from Trash.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $note = Auth::user()->notes()->onlyTrashed()->findOrFail($id);
        $note->forceDelete();

        $this->invalidateStatsCache();

        Log::info("Note {$id} permanently deleted by user " . Auth::id());

        return redirect()->route('notes.trash-list')
            ->with('success', 'Note permanently deleted.');
    }

    /**
     * Display Archived list.
     */
    public function archiveList(): View
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();
        $tags = $user->tags()->orderBy('name', 'asc')->get();

        $query = $user->notes()->where('is_archived', true)->with('tags');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($tagId = request('tag')) {
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        $query->orderBy('archived_at', 'desc');
        $notes = $query->paginate($settings->notes_per_page)->withQueryString();

        return view('notes.archive', compact('notes', 'settings', 'tags'));
    }

    /**
     * Display Trash list.
     */
    public function trashList(): View
    {
        $user = Auth::user();
        $settings = $user->getOrCreateSettings();
        $tags = $user->tags()->orderBy('name', 'asc')->get();

        $query = $user->notes()->onlyTrashed()->with('tags');

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $query->orderBy('deleted_at', 'desc');
        $notes = $query->paginate($settings->notes_per_page)->withQueryString();

        return view('notes.trash', compact('notes', 'settings', 'tags'));
    }

    /**
     * Perform Bulk action.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'note_ids' => 'required|array',
            'note_ids.*' => 'integer',
            'action' => 'required|string|in:delete,archive,restore,favorite,unfavorite,pin,unpin,force_delete',
        ]);

        $action = $validated['action'];
        
        // Find notes ensuring user ownership and including trashed notes for restore action
        $query = Auth::user()->notes()->withTrashed();
        $notes = $query->whereIn('id', $validated['note_ids'])->get();

        foreach ($notes as $note) {
            switch ($action) {
                case 'delete':
                    $note->delete();
                    break;
                case 'archive':
                    $note->update(['is_archived' => true, 'archived_at' => now()]);
                    break;
                case 'restore':
                    $note->restore();
                    break;
                case 'force_delete':
                    $note->forceDelete();
                    break;
                case 'favorite':
                    $note->update(['is_favorite' => true]);
                    break;
                case 'unfavorite':
                    $note->update(['is_favorite' => false]);
                    break;
                case 'pin':
                    $note->update(['is_pinned' => true]);
                    break;
                case 'unpin':
                    $note->update(['is_pinned' => false]);
                    break;
            }
        }

        $this->invalidateStatsCache();

        Log::info("Bulk action '{$action}' executed on notes " . implode(', ', $validated['note_ids']) . " by user " . Auth::id());

        return back()->with('success', 'Bulk action completed successfully.');
    }

    /**
     * Helper to invalidate user statistics cache.
     */
    protected function invalidateStatsCache(): void
    {
        Cache::forget("user_" . Auth::id() . "_stats");
    }
}
