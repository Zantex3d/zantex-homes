<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Landlord\DashboardController;
use App\Http\Controllers\Landlord\PropertyController;
use App\Http\Controllers\Landlord\RentCollectionController;
use App\Http\Controllers\Landlord\StatementController;
use App\Http\Controllers\Landlord\ExpenseController;
use App\Http\Controllers\Landlord\PayoutController;
use App\Http\Controllers\Landlord\DocumentController;

Route::middleware(['auth', 'landlord'])->prefix('landlord')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('landlord.dashboard');
    
    Route::get('/properties', [PropertyController::class, 'index'])->name('landlord.properties.index');
    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('landlord.properties.show');
    
    Route::get('/rent-collection', [RentCollectionController::class, 'index'])->name('landlord.rent-collection');
    Route::get('/rent-collection/export', [RentCollectionController::class, 'export'])->name('landlord.rent-collection.export');
    
    Route::get('/statements', [StatementController::class, 'index'])->name('landlord.statements');
    Route::get('/statements/download', [StatementController::class, 'download'])->name('landlord.statements.download');
    
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('landlord.expenses');
    Route::get('/expenses/pending', [ExpenseController::class, 'pending'])->name('landlord.expenses.pending');
    Route::put('/expenses/{expense}/approve', [ExpenseController::class, 'approve'])->name('landlord.expenses.approve');
    
    Route::get('/payouts', [PayoutController::class, 'index'])->name('landlord.payouts');
    Route::post('/payouts/request', [PayoutController::class, 'request'])->name('landlord.payouts.request');
    
    Route::get('/documents', [DocumentController::class, 'index'])->name('landlord.documents');
});
