<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function show(Request $request)
    {
        $mode = $request->query('mode', 'login');
        if (! in_array($mode, ['login', 'register'])) {
            $mode = 'login';
        }

        return view('auth.index', ['mode' => $mode]);
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->credentials(), $request->remember())) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->onlyInput('email');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}


