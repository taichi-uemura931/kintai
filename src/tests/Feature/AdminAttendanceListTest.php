<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\StatusSeeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StatusSeeder::class);
    }

    public function test_admin_can_view_today_attendances()
    {
        $admin = User::factory()->admin()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user1->id,
            'date' => now()->toDateString(),
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
        $response->assertSee(now()->format('Y年n月j日'));
    }
}
