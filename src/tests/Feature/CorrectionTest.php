<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Database\Seeders\StatusSeeder;

class CorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(StatusSeeder::class);
    }

    public function test_invalid_work_times_validation()
    {
        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'work_start' => '10:00',
            'work_end' => '18:00',
        ]);

        $response = $this->actingAs($user)->put("/attendance/{$attendance->id}", [
            'work_start' => '18:00',
            'work_end' => '09:00',
            'note' => '修正申請',
            'rests' => []
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_missing_note_validation()
    {
        $user = User::factory()->create(['status_id' => 1]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'work_start' => '10:00',
            'work_end' => '18:00',
        ]);

        $response = $this->actingAs($user)->put("/attendance/{$attendance->id}", [
            'work_start' => '10:00',
            'work_end' => '18:00',
            'note' => '',
            'rests' => []
        ]);

        $response->assertSessionHasErrors(['note']);
    }

    public function test_successful_update_creates_stamp_request()
    {
        $user = User::factory()->create(['status_id' => 1]);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'work_start' => '10:00',
            'work_end' => '18:00',
        ]);

        $response = $this->actingAs($user)->put("/attendance/{$attendance->id}", [
            'work_start' => '09:00',
            'work_end' => '18:00',
            'note' => '申請内容',
            'rests' => [
                ['start' => '12:00', 'end' => '13:00'],
            ]
        ]);

        $response->assertRedirect("/attendance/{$attendance->id}");
        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'reason' => '申請内容',
        ]);
    }
}
