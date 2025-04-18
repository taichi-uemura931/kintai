<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Status;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_shows_as_kimugai()
    {
        $this->seed(\Database\Seeders\StatusSeeder::class);

        $user = User::factory()->create([
            'status_id' => 1,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('勤務外');
    }
}
