<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staffUsers = User::where('is_admin', false)->get();

        return view('admin.staff.list', compact('staffUsers'));
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $yearMonth = $request->input('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($yearMonth)->startOfMonth();
        $endOfMonth = Carbon::parse($yearMonth)->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        foreach ($attendances as $attendance) {
            $totalBreakMinutes = $attendance->rests->sum(function ($rest) {
                return Carbon::parse($rest->end)->diffInMinutes(Carbon::parse($rest->start));
            });

            $totalWorkMinutes = 0;
            if ($attendance->work_start && $attendance->work_end) {
                $totalWorkMinutes = Carbon::parse($attendance->work_end)->diffInMinutes(Carbon::parse($attendance->work_start)) - $totalBreakMinutes;
            }

            $attendance->break_time_formatted = sprintf('%d時間%02d分', intdiv($totalBreakMinutes, 60), $totalBreakMinutes % 60);
            $attendance->work_time = sprintf('%d時間%02d分', intdiv($totalWorkMinutes, 60), $totalWorkMinutes % 60);
        }

        return view('admin.staff.show', [
            'user' => $user,
            'attendances' => $attendances,
            'yearMonth' => $yearMonth,
        ]);
    }

    public function exportCsv($id, Request $request)
    {
        $targetDate = $request->input('date') ?? now()->format('Y-m');
        $start = Carbon::parse($targetDate)->startOfMonth();
        $end = Carbon::parse($targetDate)->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $id)
            ->whereBetween('date', [$start, $end])
            ->get();

        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩合計', '労働時間'];

        foreach ($attendances as $a) {
            $totalBreak = $a->rests->sum(fn($r) => Carbon::parse($r->end)->diffInMinutes(Carbon::parse($r->start)));
            $totalWork = Carbon::parse($a->work_end)->diffInMinutes(Carbon::parse($a->work_start)) - $totalBreak;

            $csvData[] = [
                $a->date,
                $a->work_start,
                $a->work_end,
                sprintf('%d:%02d', intdiv($totalBreak, 60), $totalBreak % 60),
                sprintf('%d:%02d', intdiv($totalWork, 60), $totalWork % 60),
            ];
        }

        $filename = 'attendance_' . $id . '_' . $targetDate . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename={$filename}");

        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }

        fclose($handle);
        exit;
    }
}
