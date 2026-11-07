<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        return view('tenant.profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
