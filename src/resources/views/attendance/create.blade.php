@extends('layouts.app')

@section('title', '出勤登録')

@section('content')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">

@php
    $daysOfWeek = ['日', '月', '火', '水', '木', '金', '土'];
    $today = \Carbon\Carbon::today();
    $dayOfWeek = $daysOfWeek[$today->dayOfWeek];
    $dateStr = $today->format("Y年n月j日") . "（" . $dayOfWeek . "）";
    $timeStr = now()->format("H:i");
@endphp

<div class="attendance-container">
    <p class="label">{{ $statusName ?? '勤務外' }}</p>

    @if ($statusId === 1)
        <p class="date">{{ $dateStr }}</p>
        <p class="time">{{ $timeStr }}</p>
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf
            <button type="submit" class="attendance-button">出勤</button>
        </form>

    @elseif ($statusId === 2)
        <p class="date">{{ $dateStr }}</p>
        <p class="time">{{ $timeStr }}</p>
        <div class="button-group">
            <form method="POST" action="{{ route('attendance.end') }}">
                @csrf
                <button type="submit" class="checkout-button">退勤</button>
            </form>
            <form method="POST" action="{{ route('attendance.break.start') }}">
                @csrf
                <button type="submit" class="break-button">休憩入</button>
            </form>
        </div>

    @elseif ($statusId === 3)
        <p class="date">{{ $dateStr }}</p>
        <p class="time">{{ $timeStr }}</p>
        <form method="POST" action="{{ route('attendance.break.end') }}">
            @csrf
            <button type="submit" class="return-button">休憩戻</button>
        </form>

    @elseif ($statusId === 4)
        <p class="date">{{ $dateStr }}</p>
        <p class="time">{{ $timeStr }}</p>
        <p class="message">お疲れ様でした。</p>
    @endif
</div>
@endsection
