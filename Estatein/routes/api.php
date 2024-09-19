<?php

use App\Http\Controllers\Api\V1_0\PropertyController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->currentAccessToken();
})->withoutMiddleware([\Illuminate\Session\Middleware\AuthenticateSession::class,]);

Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/login' , [AuthenticatedSessionController::class, 'store']);

Route::post('/logout' , [AuthenticatedSessionController::class, 'destroy']);

Route::get('/properties' , [PropertyController::class, 'index']);

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['throttle:6,1']) // Optional middleware to throttle requests
    ->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return response()->json(['success' => true, 'message' => 'Email Verified Successfully']);
    })->middleware(['auth', 'signed'])->name('verification.verify');


