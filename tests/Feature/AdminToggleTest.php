<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_promote_user_to_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.toggle', $user));

        $response->assertSessionHas('success');
        $this->assertTrue((bool)$user->fresh()->is_admin);
    }

    public function test_admin_can_demote_admin_to_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.toggle', $user));

        $response->assertSessionHas('success');
        $this->assertFalse((bool)$user->fresh()->is_admin);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post(route('admin.users.toggle', $admin));

        $response->assertSessionHas('error');
        $this->assertTrue((bool)$admin->fresh()->is_admin);
    }

    public function test_non_admin_cannot_toggle_admin_status(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $target = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->post(route('admin.users.toggle', $target));

        $response->assertForbidden();
        $this->assertFalse((bool)$target->fresh()->is_admin);
    }
}
