<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ConstructionSite;
use App\Models\WorkDay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkTimeTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('yearlyTotal');
        $response->assertViewHas('months');
    }

    public function test_workday_edit_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $date = now()->format('Y-m-d');

        $response = $this->actingAs($user)->get("/workday/{$date}");

        $response->assertStatus(200);
        $response->assertSee('Arbeitszeit erfassen');

        // Verify that the relationship is loaded for the frontend
        $workDay = $response->viewData('workDay');
        $this->assertTrue($workDay->relationLoaded('timeEntries'));
    }

    public function test_workday_can_be_updated_with_entries(): void
    {
        $user = User::factory()->create();
        $site = ConstructionSite::create(['name' => 'Test Baustelle', 'status' => 'active']);
        $date = now()->format('Y-m-d');

        // Initial visit creates the workday record
        $this->actingAs($user)->get("/workday/{$date}");

        $workDay = WorkDay::where('user_id', $user->id)->where('date', $date)->first();

        $response = $this->actingAs($user)->put("/workday/{$workDay->id}", [
            'start_time' => '08:00',
            'end_time' => '17:00',
            'break_duration' => 45,
            'entries' => [
                [
                    'construction_site_name' => $site->name,
                    'hours' => 8.0
                ]
            ]
        ]);

        $monthUrl = route('month.show', [
            'year' => \Carbon\Carbon::parse($date)->year,
            'month' => \Carbon\Carbon::parse($date)->month
        ]);

        $response->assertRedirect($monthUrl);

        $this->assertDatabaseHas('work_days', [
            'id' => $workDay->id,
            'break_duration' => 45,
        ]);

        $this->assertDatabaseHas('time_entries', [
            'work_day_id' => $workDay->id,
            'construction_site_id' => $site->id,
            'hours' => 8.0,
        ]);
    }

    public function test_pdf_export_can_be_downloaded(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/report/download');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
