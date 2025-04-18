@extends('layouts.app')

@section('title', \Carbon\Carbon::parse($date)->format('Y年n月j日') . 'の勤怠')

@section('content')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>

<div class="attendance-list-container">
    <h2 class="title">{{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}の勤怠</h2>

    <div class="month-switcher">
        <a href="{{ route('admin.attendance.list', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}">← 前日</a>

        <div class="calendar-wrapper">
            <span class="calendar-icon" onclick="document.getElementById('calendar-input').click()">📅</span>
            <span class="calendar-label">{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</span>
            <input type="text" id="calendar-input" readonly style="display: none;" onfocus="this.blur();" />
        </div>

        <a href="{{ route('admin.attendance.list', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}">翌日 →</a>
    </div>

    <div class="attendance-table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>
                            <span class="ellipsis-cell">{{ $attendance->user->name }}</span>
                        </td>
                        <td>{{ $attendance->work_start_formatted }}</td>
                        <td>{{ $attendance->work_end_formatted }}</td>
                        <td>{{ $attendance->break_time_formatted }}</td>
                        <td>{{ $attendance->work_time }}</td>
                        <td><a href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">該当する勤怠情報がありません</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#calendar-input", {
        dateFormat: "Y/m/d",
        defaultDate: "{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}",
        appendTo: document.querySelector('.calendar-wrapper'),
        locale: "ja",
        onChange: function (selectedDates, dateStr) {
            const newDate = dateStr.replace(/\//g, '-');
            window.location.href = "{{ route('admin.attendance.list') }}?date=" + newDate;
        }
    });
});
</script>
@endpush
