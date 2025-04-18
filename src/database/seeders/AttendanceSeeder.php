<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('attendances')->insert([
            [
                'user_id' => 1,
                'date' => Carbon::create(2025, 4, 1)->toDateString(),
                'work_start' => '09:00',
                'work_end' => '18:00',
                'status_id' => 1,
                'note' => 'テスト出勤です',
                'is_pending' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
