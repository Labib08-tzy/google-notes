<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
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
            'action' => 'required|string|in:delete,archive,unarchive,restore,favorite,unfavorite,pin,unpin,force_delete',
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
                case 'unarchive':
                    $note->update(['is_archived' => false, 'archived_at' => null]);
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
     * Set or update archive PIN for a note.
     */
    public function setArchivePin(Request $request, Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'pin' => 'required|string|digits:4|confirmed',
        ]);

        $note->update(['archive_pin' => Hash::make($validated['pin'])]);

        return back()->with('success', 'PIN set successfully. Your archived note is now protected.');
    }

    /**
     * Verify archive PIN and store in session.
     */
    public function verifyArchivePin(Request $request, Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'pin' => 'required|string|digits:4',
        ]);

        if (!Hash::check($validated['pin'], $note->archive_pin)) {
            return back()->withErrors(['pin' => 'Incorrect PIN. Please try again.']);
        }

        // Store verified PIN in session for this note
        $request->session()->put('verified_pin_note_' . $note->id, true);

        return back()->with('success', 'PIN verified. Note unlocked.');
    }

    /**
     * Remove archive PIN from a note.
     */
    public function removeArchivePin(Request $request, Note $note): RedirectResponse
    {
        if ($note->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'pin' => 'required|string|digits:4',
        ]);

        if (!Hash::check($validated['pin'], $note->archive_pin)) {
            return back()->withErrors(['pin_remove' => 'Incorrect PIN. Cannot remove protection.']);
        }

        $note->update(['archive_pin' => null]);
        $request->session()->forget('verified_pin_note_' . $note->id);

        return back()->with('success', 'PIN removed. Note is no longer protected.');
    }

    /**
     * Helper to invalidate user statistics cache.
     */
    protected function invalidateStatsCache(): void
    {
        Cache::forget("user_" . Auth::id() . "_stats");
    }

    /**
     * Export note to a specific format.
     */
    public function export(Note $note, string $format): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    {
        if ($note->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $allowedFormats = ['txt', 'md', 'html', 'json'];
        if (!in_array($format, $allowedFormats)) {
            return back()->with('error', 'Invalid export format.');
        }

        $safeTitle = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $note->title);
        $filename = $safeTitle . '_' . $note->id . '.' . $format;

        switch ($format) {
            case 'txt':
                $content = $note->title . "\n" . str_repeat('=', strlen($note->title)) . "\n\n" . $note->content;
                $mime = 'text/plain';
                break;

            case 'md':
                $content = "# " . $note->title . "\n\n" . $note->content;
                if ($note->tags->count() > 0) {
                    $tags = $note->tags->pluck('name')->implode(', ');
                    $content .= "\n\n---\n**Tags:** " . $tags;
                }
                $content .= "\n\n*Created: " . $note->created_at->format('Y-m-d H:i') . "*";
                $mime = 'text/markdown';
                break;

            case 'html':
                $escapedTitle = htmlspecialchars($note->title, ENT_QUOTES, 'UTF-8');
                $escapedContent = nl2br(htmlspecialchars($note->content, ENT_QUOTES, 'UTF-8'));
                $tagsHtml = '';
                if ($note->tags->count() > 0) {
                    $tags = $note->tags->pluck('name')->map(fn($t) => '<span class="tag">' . htmlspecialchars($t) . '</span>')->implode(' ');
                    $tagsHtml = "<div class='tags'><strong>Tags:</strong> {$tags}</div>";
                }
                $content = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'><title>{$escapedTitle}</title><style>body{font-family:Georgia,serif;max-width:800px;margin:40px auto;padding:0 20px;color:#333;line-height:1.7}h1{color:#202124;border-bottom:2px solid #f59e0b;padding-bottom:10px}.meta{color:#888;font-size:0.9em;margin-top:20px}.tag{background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:12px;font-size:0.85em;margin-right:4px}</style></head><body><h1>{$escapedTitle}</h1><div class='content'>{$escapedContent}</div>{$tagsHtml}<div class='meta'>Created: {$note->created_at->format('Y-m-d H:i')}</div></body></html>";
                $mime = 'text/html';
                break;

            case 'json':
                $data = [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'tags' => $note->tags->pluck('name'),
                    'is_pinned' => $note->is_pinned,
                    'is_favorite' => $note->is_favorite,
                    'created_at' => $note->created_at->toIso8601String(),
                    'updated_at' => $note->updated_at->toIso8601String(),
                ];
                $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $mime = 'application/json';
                break;
        }

        Log::info("Note {$note->id} exported as {$format} by user " . Auth::id());

        return response($content, 200, [
            'Content-Type' => $mime . '; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
