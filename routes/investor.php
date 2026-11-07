<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Investor\DashboardController;
use App\Http\Controllers\Investor\AgreementController;
use App\Http\Controllers\Investor\ProjectController;
use App\Http\Controllers\Investor\ContributionController;
use App\Http\Controllers\Investor\DistributionController;
use App\Http\Controllers\Investor\PerformanceController;
use App\Http\Controllers\Investor\StatementController;
use App\Http\Controllers\Investor\ProfileController;

Route::middleware(['auth', 'investor'])->prefix('investor')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('investor.dashboard');
    
    Route::get('/agreements', [AgreementController::class, 'index'])->name('investor.agreements');
    Route::get('/agreements/{agreement}', [AgreementController::class, 'show'])->name('investor.agreements.show');
    
    Route::get('/projects', [ProjectController::class, 'index'])->name('investor.projects');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('investor.projects.show');
    
    Route::get('/contributions', [ContributionController::class, 'index'])->name('investor.contributions');
    Route::post('/contributions', [ContributionController::class, 'store'])->name('investor.contributions.store');
    
    Route::get('/distributions', [DistributionController::class, 'index'])->name('investor.distributions');
    
    Route::get('/performance', [PerformanceController::class, 'index'])->name('investor.performance');
    
    Route::get('/statements', [StatementController::class, 'index'])->name('investor.statements');
    Route::get('/statements/download', [StatementController::class, 'download'])->name('investor.statements.download');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('investor.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('investor.profile.update');
});
