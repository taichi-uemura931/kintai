<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;

class AdminCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = StampCorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('admin.stamp_correction_request.list', compact('requests'));
    }

    public function approve($id)
    {
        $request = StampCorrectionRequest::with(['user', 'attendance.rests'])->findOrFail($id);

        return view('admin.stamp_correction_request.approve', [
            'request' => $request,
            'attendance' => $request->attendance,
        ]);
    }

    public function approveExecute(Request $request, $id)
    {
        $correctionRequest = StampCorrectionRequest::with('attendance')->findOrFail($id);

        DB::transaction(function () use ($request, $correctionRequest) {
            $attendance = $correctionRequest->attendance;

            $attendance->update([
                'work_start' => $request->input('work_start'),
                'work_end' => $request->input('work_end'),
                'note' => $request->input('note'),
                'is_pending' => false,
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

            $correctionRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('admin.stamp_correction_request.list');
    }
}
