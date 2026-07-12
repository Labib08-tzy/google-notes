<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Updated User Name',
                'display_name' => 'My Display Name',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Updated User Name', $user->name);
        $this->assertSame('My Display Name', $user->display_name);
    }

    public function test_user_can_delete_their_account_without_password(): void
    {
        $user = User::factory()->create(['google_id' => 'google_12345']);

        $response = $this
            ->actingAs($user)
            ->delete('/profile');

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }
}
