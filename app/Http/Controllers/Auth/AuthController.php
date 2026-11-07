<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'public_id' => Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $company = Company::create([
            'public_id' => Str::uuid(),
            'legal_name' => $validated['company_name'],
            'trading_name' => $validated['company_name'],
            'base_currency' => 'NGN',
            'timezone' => 'Africa/Lagos',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $user->update(['preferred_company_id' => $company->id]);

        $role = $company->roles()->create([
            'public_id' => Str::uuid(),
            'name' => 'Admin',
            'description' => 'Company Administrator',
            'status' => 'active',
        ]);

        $user->userRoles()->create([
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);

        $user->userScopes()->create([
            'company_id' => $company->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('home'));
    }
}
