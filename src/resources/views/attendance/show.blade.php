@extends('layouts.app')

@section('title', '勤怠詳細')

@section('content')
<link rel="stylesheet" href="{{ asset('css/attendance_show.css') }}">

<div class="attendance-show-container">
    <h2 class="title">勤怠詳細</h2>

    @php
        $hasPendingRequest = \App\Models\StampCorrectionRequest::where('attendance_id', $attendance->id)
                            ->where('status', 'pending')
                            ->exists();

        if ($attendance->is_pending && !$hasPendingRequest) {
            $isPending = false;
        }

        $restsArray = $attendance->rests->toArray();
        $restsToShow = old('rests', $restsArray);

        if (empty($restsToShow)) {
            $restsToShow = [['start' => '', 'end' => '']];
        }
    @endphp

    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="attendance-table">
            <table class="attendance-detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        @if ($isPending)
                            {{ \Carbon\Carbon::parse($attendance->work_start)->format('H:i') }}
                            <span class="time-range-separator">〜</span>
                            {{ \Carbon\Carbon::parse($attendance->work_end)->format('H:i') }}
                        @else
                            <input type="time" name="work_start" value="{{ old('work_start', \Carbon\Carbon::parse($attendance->work_start)->format('H:i')) }}">
                            <span class="time-range-separator">〜</span>
                            <input type="time" name="work_end" value="{{ old('work_end', \Carbon\Carbon::parse($attendance->work_end)->format('H:i')) }}">
                        @endif
                    </td>
                </tr>

                @foreach ($restsToShow as $index => $rest)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td>
                        @if ($isPending)
                            {{ isset($rest['start']) ? \Carbon\Carbon::parse($rest['start'])->format('H:i') : '--:--' }}
                            <span class="time-range-separator">〜</span>
                            {{ isset($rest['end']) ? \Carbon\Carbon::parse($rest['end'])->format('H:i') : '--:--' }}
                        @else
                            <input type="time" name="rests[{{ $index }}][start]" value="{{ old('rests.$index.start', $rest['start'] ?? '') }}">
                            <span class="time-range-separator">〜</span>
                            <input type="time" name="rests[{{ $index }}][end]" value="{{ old('rests.$index.end', $rest['end'] ?? '') }}">
                        @endif
                    </td>
                </tr>
                @endforeach

                <tr>
                    <th>備考</th>
                    <td>
                        @if ($isPending)
                            <div class="note-display">{{ $attendance->note }}</div>
                        @else
                            <textarea name="note" class="note-textarea" rows="3">{{ old('note', $attendance->note) }}</textarea>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        @if ($errors->any())
            <div class="error-messages">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="error">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isPending)
            <div class="pending-message-container">
                <p class="pending-message">※承認待ちのため修正はできません。</p>
            </div>
        @else
            <div class="submit-container">
                <button type="submit" class="submit-button">修正</button>
            </div>
        @endif

    </form>
</div>
@endsection
