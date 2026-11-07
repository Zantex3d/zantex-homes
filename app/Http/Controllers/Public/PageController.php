<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactFormSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function about(): View
    {
        return view('public.pages.about');
    }

    public function services(): View
    {
        return view('public.pages.services');
    }

    public function investments(): View
    {
        return view('public.pages.investments');
    }

    public function contact(): View
    {
        return view('public.pages.contact');
    }

    public function storeContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        ContactFormSubmission::create($validated);

        return back()->with('success', 'Thank you! We will contact you soon.');
    }
}
