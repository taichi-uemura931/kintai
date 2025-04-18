@extends('layouts.app')

@section('title', '勤怠一覧')

@section('content')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>

<div class="attendance-list-container">
    <h2 class="title">勤怠一覧</h2>

    <div class="month-switcher">
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($yearMonth)->subMonth()->format('Y-m')]) }}">← 前月</a>
        <div class="calendar-wrapper">
            <span class="calendar-icon" onclick="document.getElementById('calendar-input').click()">📅</span>
            <span class="calendar-label">{{ \Carbon\Carbon::parse($yearMonth)->format('Y/n') }}</span>
            <input type="text" id="calendar-input" readonly style="display: none;" onfocus="this.blur();" />
        </div>
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($yearMonth)->addMonth()->format('Y-m')]) }}">翌月 →</a>
    </div>

    <div class="attendance-table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @php $weekdays = ['日', '月', '火', '水', '木', '金', '土']; @endphp
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }}（{{ $weekdays[\Carbon\Carbon::parse($attendance->date)->dayOfWeek] }}）</td>
                        <td>{{ $attendance->work_start ? \Carbon\Carbon::parse($attendance->work_start)->format('H:i') : '' }}</td>
                        <td>{{ $attendance->work_end ? \Carbon\Carbon::parse($attendance->work_end)->format('H:i') : '' }}</td>
                        <td>{{ $attendance->break_time_formatted ?? '' }}</td>
                        <td>{{ $attendance->work_time ?? '' }}</td>
                        <td><a href="{{ route('attendance.show', $attendance->id) }}">詳細</a></td>
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
    const calendar = flatpickr("#calendar-input", {
        dateFormat: "Y/m",
        defaultDate: "{{ $yearMonth }}",
        locale: "ja",
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y/m",
                altFormat: "Y年m月",
                theme: "light"
            })
        ],
        appendTo: document.querySelector('.calendar-wrapper'),
        onChange: function (selectedDates, dateStr) {
            window.location.href = "{{ route('attendance.list') }}?month=" + dateStr.replace('/', '-');
        }
    });
});
</script>
@endpush
