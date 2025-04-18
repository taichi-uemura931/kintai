@extends('layouts.app')

@section('title', 'スタッフ一覧')

@section('content')
<link rel="stylesheet" href="{{ asset('css/staff_list.css') }}">

<div class="staff-list-container">
    <h2 class="title">スタッフ一覧</h2>

    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staffUsers as $user)
                <tr>
                    <td><span class="ellipsis-cell">{{ $user->name }}</td>
                    <td><span class="ellipsis-cell">{{ $user->email }}</td>
                    <td><a href="{{ url('/admin/attendance/staff/' . $user->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
