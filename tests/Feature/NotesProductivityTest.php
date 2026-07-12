<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotesProductivityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test global search and filters.
     */
    public function test_user_can_search_notes_by_keyword(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        
        $note1 = Note::create([
            'user_id' => $user->id,
            'title' => 'Important Meeting Minutes',
            'content' => 'Discussed Phase 7 timeline.',
            'is_archived' => false,
        ]);

        $note2 = Note::create([
            'user_id' => $user->id,
            'title' => 'Shopping List',
            'content' => 'Milk, eggs, and coffee beans.',
            'is_archived' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('notes.index', ['search' => 'Meeting']));

        $response->assertStatus(200);
        $response->assertSee('Important Meeting Minutes');
        $response->assertDontSee('Shopping List');
    }

    /**
     * Test pinning notes.
     */
    public function test_pinned_notes_display_at_the_top(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);

        $noteOld = Note::create([
            'user_id' => $user->id,
            'title' => 'Old Regular Note',
            'content' => 'Regular note content.',
            'is_pinned' => false,
            'is_archived' => false,
            'created_at' => now()->subDays(5),
        ]);

        $noteNewPinned = Note::create([
            'user_id' => $user->id,
            'title' => 'New Pinned Note',
            'content' => 'Pinned note content.',
            'is_pinned' => true,
            'is_archived' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('notes.index'));

        $response->assertStatus(200);
        // Pinned note must appear before regular note
        $response->assertSeeInOrder(['New Pinned Note', 'Old Regular Note']);
    }

    /**
     * Test archiving and restoring notes.
     */
    public function test_user_can_archive_and_restore_notes(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Archive Note',
            'content' => 'Archive content.',
            'is_archived' => false,
        ]);

        // Archive
        $response = $this->actingAs($user)->patch(route('notes.archive', $note));
        $response->assertRedirect();
        $this->assertTrue($note->fresh()->is_archived);

        // Restore / Unarchive
        $response = $this->actingAs($user)->patch(route('notes.archive', $note));
        $response->assertRedirect();
        $this->assertFalse($note->fresh()->is_archived);
    }

    /**
     * Test soft deletes trash system.
     */
    public function test_soft_deletes_sends_to_trash(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Trash Note',
            'content' => 'Trash content.',
            'is_archived' => false,
        ]);

        // Destroy sends to trash (soft delete)
        $response = $this->actingAs($user)->delete(route('notes.destroy', $note));
        $response->assertRedirect();
        
        $this->assertSoftDeleted($note);
        $this->assertEquals(1, $user->notes()->onlyTrashed()->count());

        // Restore from trash
        $response = $this->actingAs($user)->patch(route('notes.restore', $note->id));
        $response->assertRedirect();
        
        $this->assertNotSoftDeleted($note);
        $this->assertEquals(0, $user->notes()->onlyTrashed()->count());

        // Force delete
        $this->actingAs($user)->delete(route('notes.destroy', $note));
        $response = $this->actingAs($user)->delete(route('notes.force-delete', $note->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    /**
     * Test tags.
     */
    public function test_user_can_manage_tags_and_filter_notes(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        $tag = Tag::create(['user_id' => $user->id, 'name' => 'Work']);

        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Tagged Note',
            'content' => 'Tagged content.',
            'is_archived' => false,
        ]);
        $note->tags()->sync([$tag->id]);

        $response = $this->actingAs($user)
            ->get(route('notes.index', ['tag' => $tag->id]));

        $response->assertStatus(200);
        $response->assertSee($note->title);
    }

    /**
     * Test bulk actions.
     */
    public function test_bulk_actions_only_affect_owned_notes(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        $otherUser = User::factory()->create(['google_id' => 'google_67890']);

        $note1 = Note::create([
            'user_id' => $user->id,
            'title' => 'Note 1',
            'content' => 'Content 1',
            'is_favorite' => false,
            'is_archived' => false,
        ]);
        
        $note2 = Note::create([
            'user_id' => $user->id,
            'title' => 'Note 2',
            'content' => 'Content 2',
            'is_favorite' => false,
            'is_archived' => false,
        ]);
        
        $otherNote = Note::create([
            'user_id' => $otherUser->id,
            'title' => 'Other Note',
            'content' => 'Other content',
            'is_favorite' => false,
            'is_archived' => false,
        ]);

        $response = $this->actingAs($user)->post(route('notes.bulk'), [
            'note_ids' => [$note1->id, $note2->id, $otherNote->id],
            'action' => 'favorite',
        ]);

        $response->assertRedirect();
        
        $this->assertTrue($note1->fresh()->is_favorite);
        $this->assertTrue($note2->fresh()->is_favorite);
        // Must not affect other user's note
        $this->assertFalse($otherNote->fresh()->is_favorite);
    }

    /**
     * Test autosave endpoint updates database.
     */
    public function test_autosave_updates_note_content(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'content' => 'Original Content',
            'is_archived' => false,
        ]);

        $response = $this->actingAs($user)->patch(route('notes.autosave', $note), [
            'title' => 'Autosaved Title',
            'content' => 'Autosaved Content',
        ]);

        $response->assertJson(['success' => true]);
        $this->assertEquals('Autosaved Title', $note->fresh()->title);
        $this->assertEquals('Autosaved Content', $note->fresh()->content);
    }

    /**
     * Test cache is invalidated when notes are modified.
     */
    public function test_cache_is_invalidated_on_note_change(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);
        
        $cacheKey = "user_{$user->id}_stats";
        \Illuminate\Support\Facades\Cache::put($cacheKey, ['totalNotes' => 10], now()->addHour());

        $this->assertEquals(10, \Illuminate\Support\Facades\Cache::get($cacheKey)['totalNotes']);

        // Creating a note should invalidate the cache
        $this->actingAs($user)->post(route('notes.store'), [
            'title' => 'Cache Invalidation test',
            'content' => 'Content here',
        ]);

        $this->assertNull(\Illuminate\Support\Facades\Cache::get($cacheKey));
    }

    /**
     * Test rate limiting on AI endpoints.
     */
    public function test_ai_endpoints_rate_limiting(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);

        // Mock GeminiService to prevent actual network calls during rate limiting tests
        $mockService = $this->mock(\App\Services\GeminiService::class);
        $mockService->shouldReceive('summarize')->andReturn('summary text');

        // Execute 5 requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($user)->post(route('ai.summarize'), [
                'content' => 'some content to summarize',
            ]);
            $response->assertStatus(200);
        }

        // The 6th request should be rate limited (429)
        $response = $this->actingAs($user)->post(route('ai.summarize'), [
            'content' => 'some content to summarize',
        ]);
        $response->assertStatus(429);
        $response->assertJsonStructure(['error']);
    }
}
