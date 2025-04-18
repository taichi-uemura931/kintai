@extends('layouts.app')

@section('title','管理者用ログインフォーム')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">

    <div class="login-container">
        <h2 class="login-title">管理者用ログイン</h2>

        <form method="POST" action="{{ route('admin.login.post') }}" novalidate>
            @csrf

            <div class="form-group">
                <label for="email">メールアドレス</label><br>
                <input type="email" name="email" value="{{ old('email') }}" class="input-field"><br>
                @error('email')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label><br>
                <input type="password" name="password" class="input-field"><br>
                @error('password')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div><br>

            <button type="submit"  class="login-button">管理者ログインする</button>
        </form>
    </div>

@endsection