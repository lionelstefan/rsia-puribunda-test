<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;


class LoginController extends Controller
{
	public function login()
	{
		return view('login');
	}

	public function checkLogin(Request $request)
	{

		$credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

		$user = User::where('username', $credentials['username'])->first();

        if ($credentials['password'] === $user->password) {
            $request->session()->regenerate();
			Auth::login($user);
 
            return redirect()->route('backend.home');
        }
 
        return back()->withFail('Username / Password salah !');
	}

	public function logout(Request $request)
	{
		Auth::logout();
		return redirect('/');
	}
}
