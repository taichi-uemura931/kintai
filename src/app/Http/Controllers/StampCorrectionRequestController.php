<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\DB;

class StampCorrectionRequestController extends Controller
{
    public function list(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = StampCorrectionRequest::with(['user', 'attendance'])
            ->where('user_id', auth()->id())
            ->where('status', $status)
            ->get();

        return view('stamp_correction_request.list', compact('requests'));
    }

    public function approve($id)
    {
        DB::transaction(function () use ($id) {
            $request = StampCorrectionRequest::with('attendance')->findOrFail($id);

            $request->update([
                'status' => '承認済み',
                'approved_at' => now(),
            ]);

            $request->attendance->update([
                'is_pending' => false,
            ]);
        });

        return redirect()->back();
    }
}
