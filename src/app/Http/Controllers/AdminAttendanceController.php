<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function list(Request $request)
    {
        $date = $request->input('date') ?? now()->toDateString();

        $attendances = Attendance::with(['user', 'rests'])
            ->where('date', $date)
            ->get()
            ->map(function ($attendance) {
                $workStart = $attendance->work_start ? \Carbon\Carbon::parse($attendance->work_start) : null;
                $workEnd = $attendance->work_end ? \Carbon\Carbon::parse($attendance->work_end) : null;

                $totalBreakMinutes = $attendance->rests->reduce(function ($carry, $rest) {
                    if ($rest->start && $rest->end) {
                        return $carry + \Carbon\Carbon::parse($rest->end)->diffInMinutes(\Carbon\Carbon::parse($rest->start));
                    }
                    return $carry;
                }, 0);

                $attendance->work_start_formatted = $workStart ? $workStart->format('H:i') : '-';
                $attendance->work_end_formatted = $workEnd ? $workEnd->format('H:i') : '-';

                $attendance->break_time_formatted = $totalBreakMinutes > 0
                    ? sprintf('%d時間%02d分', intdiv($totalBreakMinutes, 60), $totalBreakMinutes % 60)
                    : '-';

                if ($workStart && $workEnd) {
                    $totalMinutes = $workEnd->diffInMinutes($workStart) - $totalBreakMinutes;
                    $attendance->work_time = sprintf('%d時間%02d分', intdiv($totalMinutes, 60), $totalMinutes % 60);
                } else {
                    $attendance->work_time = '-';
                }

                return $attendance;
            });

        return view('admin.attendance.list', compact('attendances', 'date'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);

        $isPending = $attendance->stampCorrectionRequests()
        ->where('status', 'pending')
        ->exists();

        return view('attendance.show', compact('attendance','isPending'));
    }
}
