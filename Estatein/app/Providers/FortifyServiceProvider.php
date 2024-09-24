<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Contracts\PasswordResetResponse;
use Laravel\Fortify\Contracts\ResetPasswordViewResponse;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->instance(RegisterResponse::class , new class implements RegisterResponse{
            public function toResponse($request)
            {
                return response()->json([
                    "message" => "Register Successfully",
                    "name" => $request->input('name')
                ], 200 );
            }
        });
        $this->app->instance(LoginResponse::class , new class implements LoginResponse{
            public function toResponse($request)
            {
                $token = $request->user()->createToken('auth_token');

                return response()->json([
                    "message" => "Logged in Successfully",
                    "name" => $request->input('email'),
                    "token" => $token->plainTextToken
                ], 200 );
            }
        });
        $this->app->instance(LogoutResponse::class , new class implements LogoutResponse{
            public function toResponse($request)
            {

                return response()->json([
                    "message" => "Logged out Successfully",
                ], 200 );
            }
        });


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fortify::ignoreRoutes();
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);


        // Fortify::verifyEmailView(function () {
        //     return redirect('http://localhost:5173/email-verification');
        // });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
