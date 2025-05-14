<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);
        // Determine if login is email or username
        $loginType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $authCredentials = [
            $loginType => $credentials['login'],
            'password' => $credentials['password']
        ];
        if (Auth::attempt($authCredentials)) {
            $request->session()->regenerate();
            return response()->json(['message' => 'Login successful', 'user' => Auth::user()]);
        } else {
            return response()->json(['message' => 'Invalid username or Password'], 401);
        }
    }
}
