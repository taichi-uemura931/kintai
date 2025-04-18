@extends('layouts.app')

@section('title', '勤怠詳細（管理者）')

@section('content')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">

@php
    $restsArray = $attendance->rests->toArray();
    if (empty($restsArray)) {
        $restsArray = [['start' => '', 'end' => '']];
    }
@endphp

<div class="attendance-detail-container">
    <h2 class="title">勤怠詳細</h2>

    <form method="POST" action="{{ route('admin.stamp_correction_request.approve.execute', $request->id) }}">
        @csrf

        <div class="attendance-table">
            <table class="attendance-detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $request->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        @if ($request->status === 'approved')
                            <span>{{ \Carbon\Carbon::parse($attendance->work_start)->format('H:i') }}</span>
                            <span class="time-range-separator">〜</span>
                            <span>{{ \Carbon\Carbon::parse($attendance->work_end)->format('H:i') }}</span>
                        @else
                            <input type="time" name="work_start" value="{{ $attendance->work_start }}">
                            <span class="time-range-separator">〜</span>
                            <input type="time" name="work_end" value="{{ $attendance->work_end }}">
                        @endif
                    </td>
                </tr>

                @foreach ($restsArray as $index => $rest)
                    <tr>
                        <th>休憩{{ $index + 1 }}</th>
                        <td>
                            @if ($request->status === 'approved')
                                <span>
                                    {{ isset($rest['start']) ? \Carbon\Carbon::parse($rest['start'])->format('H:i') : '--:--' }}
                                </span>
                                <span class="time-range-separator">〜</span>
                                <span>
                                    {{ isset($rest['end']) ? \Carbon\Carbon::parse($rest['end'])->format('H:i') : '--:--' }}
                                </span>
                            @else
                                <input type="time" name="rests[{{ $index }}][start]" value="{{ $rest['start'] ?? '' }}">
                                <span class="time-range-separator">〜</span>
                                <input type="time" name="rests[{{ $index }}][end]" value="{{ $rest['end'] ?? '' }}">
                            @endif
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <th>備考</th>
                    <td>
                        @if ($request->status === 'approved')
                            <div class="note-display">{{ $attendance->note }}</div>
                        @else
                            <textarea name="note" class="note-textarea" rows="3">{{ $attendance->note }}</textarea>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="form-footer">
            @if ($request->status !== 'approved')
                <button type="submit" class="submit-button">承認</button>
            @else
                <span class="submit-button" style="background-color: #ccc; cursor: default;">承認済み</span>
            @endif
        </div>
    </form>
</div>
@endsection
