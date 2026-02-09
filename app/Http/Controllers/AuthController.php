<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function signin(){

        // $credentials = $request->validate([
        //     'email' => ['required', 'email'],
        //     'password' => ['required']
        // ]);

        // if(Auth::attempt($credentials)){
        //     $request->session()->regenerate();

        //     return redirect()->intended("/");
        // }

        // return back()->withErrors([
        //     'email' => 'The provided credentials do not match our records.',
        // ])->onlyInput('email');

        return response()->json([
            'message' => "he;llo wildan"
        ]);

    }

    public function signout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
