<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_language()
    {
        $user = User::factory()->create(['language' => 'de']);

        $response = $this->actingAs($user)
            ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'language' => 'en',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $this->assertEquals('en', $user->fresh()->language);

        // Verify localization logic (rudimentary check if app locale is set)
        $this->actingAs($user)->get('/profile');
        $this->assertEquals('en', app()->getLocale());

        $user->update(['language' => 'de']);
        $this->actingAs($user)->get('/profile');
        $this->assertEquals('de', app()->getLocale());
    }

    public function test_user_cannot_delete_own_account()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertStatus(403);
        $this->assertNotNull($user->fresh());
    }
}
