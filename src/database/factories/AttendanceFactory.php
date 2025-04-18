<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'date' => today(),
            'work_start' => '09:00',
            'work_end' => '18:00',
            'note' => '勤務記録',
            'is_pending' => false,
        ];
    }
}
