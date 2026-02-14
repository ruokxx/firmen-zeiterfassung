<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_regular_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_admin_can_download_user_report(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        // Create some data
        $workDay = $user->workDays()->create([
            'date' => now()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '16:30',
            'break_duration' => 30,
        ]);

        $response = $this->actingAs($admin)->get(route('report.download', [
            'year' => now()->year,
            'month' => now()->month,
            'user_id' => $user->id
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
