<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForcedPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_change_password_is_redirected()
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('admin.setup.create'));
    }

    public function test_user_can_access_setup_page()
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $response = $this->actingAs($user)->get(route('admin.setup.create'));

        $response->assertStatus(200);
    }

    public function test_user_can_update_account_and_is_unlocked()
    {
        $user = User::factory()->create([
            'must_change_password' => true,
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->post(route('admin.setup.store'), [
            'email' => 'new-email@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse((bool)$user->must_change_password);
        $this->assertEquals('new-email@example.com', $user->email);
        $this->assertTrue(Hash::check('new-password', $user->password));
    }
}
