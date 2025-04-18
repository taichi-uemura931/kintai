<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => 'ログイン情報が正しくありません。',
            ]);
        }

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user->is_admin) {
            return back()->withErrors([
                'email' => '管理者用ログイン画面からはログインできません。',
            ]);
        }

        Auth::attempt($credentials);

        $request->session()->regenerate();
        $request->session()->forget('errors');

        return redirect()->route('admin.attendance.list');
    }
}
