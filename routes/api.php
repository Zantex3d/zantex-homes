<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\JournalController;
use App\Http\Controllers\Webhook\PaystackWebhookController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->load('companies', 'userRoles.role'));
    });

    Route::apiResource('properties', PropertyController::class);
    
    Route::apiResource('journals', JournalController::class)->only(['index', 'show', 'store']);
    Route::post('journals/{journal}/post', [JournalController::class, 'post']);
    Route::post('journals/{journal}/reverse', [JournalController::class, 'reverse']);
    Route::get('accounting/trial-balance', [JournalController::class, 'trialBalance']);
});

// Public webhook endpoints (no authentication)
Route::post('/webhook/paystack', [PaystackWebhookController::class, 'handle'])->name('webhook.paystack');

