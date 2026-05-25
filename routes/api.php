<?php

use App\Http\Controllers\MobileNasabahAuthController;
use App\Http\Controllers\NasabahTopupController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile/nasabah')->group(function () {
    Route::post('/register', [MobileNasabahAuthController::class, 'register'])
        ->middleware('throttle:5,1');

    Route::post('/email-verification/resend', [MobileNasabahAuthController::class, 'resendVerification'])
        ->middleware('throttle:3,1');

    Route::post('/password-reset', [MobileNasabahAuthController::class, 'sendPasswordReset'])
        ->middleware('throttle:3,1');

    Route::post('/verify-login', [MobileNasabahAuthController::class, 'verifyLogin'])
        ->middleware('throttle:10,1');

    Route::post('/topup', [NasabahTopupController::class, 'create'])
        ->middleware('throttle:10,1');
});
