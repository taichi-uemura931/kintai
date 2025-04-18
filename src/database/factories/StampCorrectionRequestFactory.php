<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'attendance_id' => Attendance::factory(),
            'reason' => '修正申請理由',
            'status' => 'pending',
            'requested_at' => now(),
            'approved_at' => null,
        ];
    }
}
