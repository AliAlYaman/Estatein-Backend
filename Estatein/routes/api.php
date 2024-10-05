<?php

use App\Http\Controllers\Api\V1_0\PropertyController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->currentAccessToken();
})->withoutMiddleware([\Illuminate\Session\Middleware\AuthenticateSession::class,]);

Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/login' , [AuthenticatedSessionController::class, 'store']);

Route::post('/logout' , [AuthenticatedSessionController::class, 'destroy']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.reset');

Route::get('/reset-password', function (Request $request) {
    $token = $request->query('token');  // Access the token from the query parameters
    $email = $request->query('email');  // Access the email from the query parameters

    // Now you can pass $token and $email to your frontend, for example:
    return redirect("http://localhost:5173/reset-password?token={$token}&email={$email}");
})->name('password.view');

Route::get('/properties' , [PropertyController::class, 'index']);
Route::post('/search-properties' , [PropertyController::class, 'filter']);

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['throttle:6,1']) // Optional middleware to throttle requests
    ->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return response()->json(['success' => true, 'message' => 'Email Verified Successfully']);
    })->middleware(['auth', 'signed'])->name('verification.verify');


