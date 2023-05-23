<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
	public function login()
	{
		return view('login');
	}

	public function checkLogin(Request $request)
	{
		$user_name = $request->input('user_name');
		$password  = $request->input('password');

		if ($user_name == 'backadmin' && $password == 'puribunda') {
			session([
				'username' 	=> $user_name,
			]);
			return redirect()->route('backend.home');
		} else {
			return Redirect::to('/')->withFail('Username / password salah!');
		}
	}

	public function logout(Request $request)
	{
		Session::flush();
		return redirect('/');
	}
}
