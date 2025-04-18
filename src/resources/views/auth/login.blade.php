@extends('layouts.app')

@section('title','ログインフォーム')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <div class="login-container">
        <h2 class="login-title">ログイン</h2>

        <form method="POST" action="{{ route('login.post') }}" novalidate>
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

            <button type="submit"  class="login-button">ログイン</button>
        </form>

        <a href="{{ route('register') }}" class="register-link">会員登録はこちら</a>
    </div>

@endsection