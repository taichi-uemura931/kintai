<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $data['is_admin'] = false;

        $user = User::create($data);

        auth()->login($user);

        event(new Registered($user));

        return redirect('/email/verify')->with('success', '登録が完了しました');
    }
}
