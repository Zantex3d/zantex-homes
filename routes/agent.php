<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\LeadController;
use App\Http\Controllers\Agent\ViewingController;
use App\Http\Controllers\Agent\ReservationController;
use App\Http\Controllers\Agent\CommissionController;
use App\Http\Controllers\Agent\PerformanceController;
use App\Http\Controllers\Agent\ProfileController;

Route::middleware(['auth', 'agent'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('agent.dashboard');
    
    Route::get('/leads', [LeadController::class, 'index'])->name('agent.leads');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('agent.leads.show');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'addNote'])->name('agent.leads.note');
    Route::post('/leads/{lead}/schedule', [LeadController::class, 'schedule'])->name('agent.leads.schedule');
    
    Route::get('/viewings', [ViewingController::class, 'index'])->name('agent.viewings');
    Route::get('/viewings/{viewing}', [ViewingController::class, 'show'])->name('agent.viewings.show');
    Route::post('/viewings/{viewing}/feedback', [ViewingController::class, 'storeFeedback'])->name('agent.viewings.feedback');
    Route::put('/viewings/{viewing}/complete', [ViewingController::class, 'complete'])->name('agent.viewings.complete');
    
    Route::get('/reservations', [ReservationController::class, 'index'])->name('agent.reservations');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('agent.reservations.show');
    
    Route::get('/commissions', [CommissionController::class, 'index'])->name('agent.commissions');
    
    Route::get('/performance', [PerformanceController::class, 'index'])->name('agent.performance');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('agent.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('agent.profile.update');
});
