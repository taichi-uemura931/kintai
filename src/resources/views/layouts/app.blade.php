<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '会員登録サイト')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>

<body style="overflow: auto;">
    <div class="wrapper">

        <header class="header">
            <div class="header-container">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="COACHTECHロゴ" class="logo">
                </a>

                @if (!request()->is('register') && !request()->is('login') && !request()->is('email/verify') && !request()->is('admin/login') && Auth::check())
                    <nav class="nav-menu">
                        @if (Auth::user()->is_admin)
                            <a href="{{ url('/admin/attendance/list') }}">勤怠一覧</a>
                            <a href="{{ url('/admin/staff/list') }}">スタッフ一覧</a>
                            <a href="{{ route('admin.stamp_correction_request.list') }}">申請一覧</a>
                        @else
                            <a href="{{ route('attendance') }}">勤怠</a>
                            <a href="{{ route('attendance.list') }}">勤怠一覧</a>
                            <a href="{{ route('stamp_correction_request.list') }}">申請</a>
                        @endif
                        <a href="{{ Auth::user()->is_admin ? route('admin.logout') : route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            ログアウト
                        </a>
                        <form id="logout-form"
                            action="{{ Auth::user()->is_admin ? route('admin.logout') : route('logout') }}"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                    </nav>
                @endif
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>

</html>
