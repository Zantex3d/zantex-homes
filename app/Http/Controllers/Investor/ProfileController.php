<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        return view('investor.profile', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'bank_account' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }
}
