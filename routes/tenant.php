<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\LeaseController;
use App\Http\Controllers\Tenant\RentController;
use App\Http\Controllers\Tenant\MaintenanceController;
use App\Http\Controllers\Tenant\DocumentController;
use App\Http\Controllers\Tenant\ProfileController;

Route::middleware(['auth', 'tenant'])->prefix('tenant')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');
    
    Route::get('/lease', [LeaseController::class, 'index'])->name('tenant.lease');
    Route::get('/lease/download', [LeaseController::class, 'downloadPdf'])->name('tenant.lease.download');
    
    Route::get('/rent-invoices', [RentController::class, 'invoices'])->name('tenant.rent.invoices');
    Route::get('/payments', [RentController::class, 'payments'])->name('tenant.rent.payments');
    Route::get('/service-charges', [RentController::class, 'serviceCharges'])->name('tenant.service-charges');
    Route::post('/pay-invoice/{invoice}', [RentController::class, 'initiatePayment'])->name('tenant.rent.pay');
    
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('tenant.maintenance.index');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('tenant.maintenance.store');
    Route::get('/maintenance/{ticket}', [MaintenanceController::class, 'show'])->name('tenant.maintenance.show');
    
    Route::get('/documents', [DocumentController::class, 'index'])->name('tenant.documents');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('tenant.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('tenant.profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('tenant.profile.password');
});
