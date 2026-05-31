<?php

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\MobileNasabahAuthController;
use App\Http\Controllers\MobileNasabahDataController;
use App\Http\Controllers\NasabahTransaksiSetorController;
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

    Route::get('/lookup', [MobileNasabahDataController::class, 'lookup'])
        ->middleware('throttle:60,1');

    Route::get('/email-availability', [MobileNasabahDataController::class, 'emailAvailability'])
        ->middleware('throttle:60,1');

    Route::post('/mirror-profile', [MobileNasabahDataController::class, 'mirrorProfile'])
        ->middleware('throttle:30,1');

    Route::get('/waste-types', [MobileNasabahDataController::class, 'wasteTypes'])
        ->middleware('throttle:60,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MobileNasabahAuthController::class, 'logout'])
            ->middleware('throttle:20,1');

        Route::post('/topup', [NasabahTopupController::class, 'create'])
            ->middleware('throttle:10,1');

        Route::get('/profile', [MobileNasabahDataController::class, 'profile'])
            ->middleware('throttle:60,1');

        Route::patch('/profile', [MobileNasabahDataController::class, 'updateProfile'])
            ->middleware('throttle:20,1');

        Route::patch('/password-marker', [MobileNasabahDataController::class, 'markPasswordManaged'])
            ->middleware('throttle:30,1');

        Route::post('/chatbot/message', [ChatbotController::class, 'sendMobile'])
            ->middleware('throttle:20,1');

        Route::post('/validate-waste-photo', [NasabahTransaksiSetorController::class, 'detectWastePhoto'])
            ->middleware('throttle:10,1');

        Route::post('/setor', [MobileNasabahDataController::class, 'storeSetor'])
            ->middleware('throttle:10,1');

        Route::get('/setor-history', [MobileNasabahDataController::class, 'setorHistory'])
            ->middleware('throttle:60,1');

        Route::post('/ppob', [MobileNasabahDataController::class, 'storePpob'])
            ->middleware('throttle:20,1');

        Route::get('/ppob-transactions', [MobileNasabahDataController::class, 'ppobTransactions'])
            ->middleware('throttle:60,1');

        Route::get('/dashboard', [MobileNasabahDataController::class, 'dashboard'])
            ->middleware('throttle:60,1');

        Route::get('/topups', [MobileNasabahDataController::class, 'topupHistory'])
            ->middleware('throttle:60,1');

        Route::get('/topup/status', [NasabahTopupController::class, 'checkStatus'])
            ->middleware('throttle:60,1');
    });
});
