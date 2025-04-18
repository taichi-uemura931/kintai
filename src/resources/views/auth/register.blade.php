@extends('layouts.app')

@section('title','会員登録フォーム')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/register.css') }}">

    <div class="register-container">
        <h2 class="register-title">会員登録</h2>

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="form-group">
                <label for="name">ユーザー名</label><br>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field"><br>
                @error('name')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

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
            </div>

            <div class="form-group">
                <label for="password_confirmation">確認用パスワード</label><br>
                <input type="password" name="password_confirmation" class="input-field"><br><br>
                @error('password_confirmation')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div><br>

            <button type="submit" class="register-button">登録する</button>
        </form>

        <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
    </div>

@endsection