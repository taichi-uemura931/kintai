<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => 'ログイン情報が正しくありません。',
            ]);
        }

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user->is_admin) {
            return back()->withErrors([
                'email' => '一般ユーザー用ログイン画面からはログインできません。',
            ]);
        }

        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect('/attendance');
    }
}
