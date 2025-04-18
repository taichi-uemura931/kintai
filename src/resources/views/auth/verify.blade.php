@extends('layouts.app')

@section('title', 'メール認証')

@section('content')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">

<div class="verify-container">
    <h2>登録していただいたメールアドレスに認証メールを送付しました</h2>
    <p>メール認証を完了してください</p>

    <form method="POST" action="{{ route('verification.redirect-send') }}">
        @csrf
        <button type="submit" class="verify-button">認証はこちらから</button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="resend-link">認証メールを再送する</button>
    </form>

    @if (session('message'))
        <p class="resend-message">{{ session('message') }}</p>
    @endif
</div>
@endsection