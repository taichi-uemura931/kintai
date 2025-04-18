<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Attendance;

class RestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attendance = Attendance::where('user_id', 1)
            ->where('date', '2025-04-01')
            ->first();

        if ($attendance) {
            DB::table('rests')->insert([
                'attendance_id' => $attendance->id,
                'start' => '12:00',
                'end' => '13:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
