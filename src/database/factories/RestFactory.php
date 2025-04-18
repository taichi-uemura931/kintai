<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestFactory extends Factory
{
    protected $model = Rest::class;

    public function definition()

    {
        return [
            'attendance_id' => Attendance::factory(),
            'start' => '12:00',
            'end' => '13:00',
        ];
    }
}
