<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Http\Requests\AttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.attendance.list');
        }

        $user = \App\Models\User::find(Auth::id());

        $today = now()->toDateString();

        $hasTodayAttendance = Attendance::where('user_id', $user->id)
        ->where('date', $today)
        ->exists();

        if (!$hasTodayAttendance) {
            $statusId = 1;
            $statusName = '勤務外';
        } else {
            $statusId = $user->status_id;
            $statusName = $user->status->name;
        }

        return view('attendance.create', compact('statusId', 'statusName'));
    }

    public function store(AttendanceRequest $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $today = now()->toDateString();

        $alreadyExists = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->exists();

        if (!$alreadyExists) {
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'work_start' => now()->format('H:i'),
            ]);
        }
        $user->update(['status_id' => 2]);

        return redirect()->route('attendance');
    }

    public function list(Request $request)
    {
        $user = auth()->user();

        $yearMonth = $request->input('month') ?? now()->format('Y-m');
        $startOfMonth = Carbon::parse($yearMonth . '-01')->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $workStart = $attendance->work_start ? Carbon::parse($attendance->work_start) : null;
                $workEnd = $attendance->work_end ? Carbon::parse($attendance->work_end) : null;

                $totalBreakMinutes = $attendance->rests->reduce(function ($carry, $rest) {
                    if ($rest->start && $rest->end) {
                        return $carry + Carbon::parse($rest->end)->diffInMinutes(Carbon::parse($rest->start));
                    }
                    return $carry;
                }, 0);

                $attendance->break_time_formatted = $totalBreakMinutes > 0
                ? sprintf('%d時間%02d分', intdiv($totalBreakMinutes, 60), $totalBreakMinutes % 60)
                : '';

                if ($workStart && $workEnd) {
                    $totalMinutes = $workEnd->diffInMinutes($workStart) - $totalBreakMinutes;
                    $attendance->work_time = sprintf('%d時間%02d分', intdiv($totalMinutes, 60), $totalMinutes % 60);
                } else {
                    $attendance->work_time = '';
                }

                return $attendance;
            });

        return view('attendance.list', compact('attendances', 'yearMonth'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('rests')->findOrFail($id);
        $user = Auth::user();

        $isPending = $attendance->stampCorrectionRequests()
            ->where('status', 'pending')
            ->exists();

        return view('attendance.show', compact('attendance','user','isPending'));
    }

    public function update(UpdateAttendanceRequest $request, $id)
    {
        $attendance = Attendance::with('rests')->findOrFail($id);

        DB::transaction(function () use ($request, $attendance) {
            $attendance->update([
                'work_start' => $request->input('work_start'),
                'work_end' => $request->input('work_end'),
                'note' => $request->input('note'),
                'is_pending' => true,
            ]);

            $attendance->rests()->delete();
            foreach ($request->input('rests', []) as $rest) {
                if (!empty($rest['start']) && !empty($rest['end'])) {
                    $attendance->rests()->create([
                        'start' => $rest['start'],
                        'end' => $rest['end'],
                    ]);
                }
            }

            StampCorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'reason' => $request->input('note'),
            ]);
        });

        return redirect()->route('attendance.show', ['id' => $attendance->id]);
    }

    public function startBreak()
    {
        $user = Auth::user();

        $attendance = $this->getTodayAttendance();

        $attendance->rests()->create([
            'start' => now()->format('H:i'),
        ]);

        $user->update(['status_id' => 3]);

        return redirect()->route('attendance');
    }

    public function endBreak()
    {
        $user = Auth::user();

        $attendance = $this->getTodayAttendance();

        $latestRest = $attendance->rests()->whereNull('end')->latest()->first();
        if ($latestRest) {
            $latestRest->update([
                'end' => now()->format('H:i'),
            ]);
        }

        $user->update(['status_id' => 2]);

        return redirect()->route('attendance');
    }

    public function endWork()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', now()->toDateString())
            ->firstOrFail();

        $attendance->update(['work_end' => now()->format('H:i')]);

        auth()->user()->update(['status_id' => 4]);

        return redirect()->route('attendance');
    }

    private function getTodayAttendance()
    {
        return Attendance::where('user_id', auth()->id())
            ->where('date', now()->toDateString())
            ->firstOrFail();
    }

    public function create()
    {
        return view('attendance.create');
    }
}
