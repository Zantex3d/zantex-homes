<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PropertyController;
use App\Http\Controllers\Public\LandController;
use App\Http\Controllers\Public\ShortletController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\PageController;

// Public portal routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/properties/search', [PropertyController::class, 'search'])->name('properties.search');
Route::get('/properties/{slug}', [PropertyController::class, 'show'])->name('properties.show');
Route::post('/properties/{slug}/inquiry', [PropertyController::class, 'storeInquiry'])->name('properties.inquiry');
Route::post('/properties/{slug}/favorite', [PropertyController::class, 'toggleFavorite'])->middleware('auth')->name('properties.favorite');

Route::get('/land', [LandController::class, 'index'])->name('land.index');
Route::get('/shortlets', [ShortletController::class, 'index'])->name('shortlets.index');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/investments', [PageController::class, 'investments'])->name('investments');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');

Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.store');

Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('register.store');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('admin')->group(function () {
        Route::get('/properties', fn() => view('admin.properties.index'))->name('admin.properties.index');
        Route::get('/accounting/journals', fn() => view('admin.accounting.journals'))->name('admin.accounting.journals');
        Route::get('/accounting/trial-balance', fn() => view('admin.accounting.trial-balance'))->name('admin.accounting.trial-balance');
    });
});

// Portal route groups
require base_path('routes/tenant.php');
require base_path('routes/landlord.php');
require base_path('routes/investor.php');
require base_path('routes/agent.php');

