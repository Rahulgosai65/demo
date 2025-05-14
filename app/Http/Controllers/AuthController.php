<?php

namespace App\Http\Controllers;

use App\DataTables\CustomerDataTable;
use App\Facades\UtilityFacades;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RewardPoint;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use File;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;


class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
        ]);

        return redirect()->back()->with('success', 'Registration successful. Please check your email for verification.');
    }

    public function loginprocess(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Please verify your email before login.');
            }

            return redirect('/dashboard');
        }

        return back()->with('error', 'Invalid email or password.');
    }
}