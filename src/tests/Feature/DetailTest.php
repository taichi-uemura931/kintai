<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Database\Seeders\StatusSeeder;
use Carbon\Carbon;

class DetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_details_shown_correctly()
    {
        $this->seed(StatusSeeder::class);

        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'date' => Carbon::today(),
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");

        $response->assertStatus(200)
                ->assertSee($user->name)
                ->assertSee('09:00')
                ->assertSee('18:00');
    }
}
