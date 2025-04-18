@extends('layouts.app')

@section('title', '申請一覧（管理者）')

@section('content')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">

<div class="request-list-container">
    <h2 class="title">申請一覧</h2>

    <div class="request-list-nav">
        <a href="{{ route('admin.stamp_correction_request.list', ['status' => 'pending']) }}"
        class="pending-tab {{ request('status') === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.stamp_correction_request.list', ['status' => 'approved']) }}"
        class="{{ request('status') === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="request-table-container">
        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                        <td><span class="ellipsis-cell">{{ $request->user->name }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}</td>
                        <td><span class="ellipsis-cell">{{ $request->reason }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($request->requested_at)->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('admin.stamp_correction_request.approve', ['attendance_correct_request' => $request->id]) }}">詳細</a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
