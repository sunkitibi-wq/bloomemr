<?php

namespace App\Providers;

use App\Events\LabResultReceived;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegisterResponse;
use App\Listeners\InjectLabSummaryIntoEncounter;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(TwoFactorLoginResponseContract::class, LoginResponse::class);

        $this->app->bind(StatefulGuard::class, function ($app) {
            $request = $app['request'];
            $guard = 'web';

            if ($request->is('register', 'patient/register') && $request->isMethod('POST')) {
                $role = $request->input('role');
                if ($role === 'guardian') {
                    $guard = 'portal';
                }
            } elseif ($request->is('login') && $request->isMethod('POST')) {
                // Patient portal login form sends portal_login=1 as a fast-path signal
                if ($request->boolean('portal_login')) {
                    $guard = 'portal';
                } else {
                    $email = $request->input('email');
                    if ($email) {
                        $user = User::where('email', $email)->first();
                        if ($user && $user->role === 'guardian') {
                            $guard = 'portal';
                        }
                    }
                }
            } elseif ($request->is('two-factor-challenge') && $request->isMethod('POST')) {
                $userId = $request->session()->get('login.id');
                if ($userId) {
                    $user = User::find($userId);
                    if ($user && $user->role === 'guardian') {
                        $guard = 'portal';
                    }
                }
            } elseif ($request->is('passkeys/login') && $request->isMethod('POST')) {
                $credentialId = $request->input('id');
                if ($credentialId) {
                    $userId = \DB::table('passkeys')->where('credential_id', $credentialId)->value('user_id');
                    if ($userId) {
                        $user = User::find($userId);
                        if ($user && $user->role === 'guardian') {
                            $guard = 'portal';
                        }
                    }
                }
            } elseif ($request->is('logout') && $request->isMethod('POST')) {
                if (auth('portal')->check()) {
                    $guard = 'portal';
                }
            }

            if ($guard === 'portal') {
                config([
                    'fortify.guard' => 'portal',
                    'passkeys.guard' => 'portal',
                    'passkeys.redirect' => '/portal',
                ]);
            }

            return $app['auth']->guard($guard);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Passport::authorizationView('passport.authorize');
        Passport::enablePasswordGrant();

        Gate::before(function ($user, $ability) {
            if ($user->isSystemAdmin()) {
                return true;
            }

            return $user->hasRole('super_admin') ? true : null;
        });

        Gate::define('system_admin', fn ($user) => $user->isSystemAdmin());

        Event::listen(
            LabResultReceived::class,
            InjectLabSummaryIntoEncounter::class
        );

        RedirectIfAuthenticated::redirectUsing(function () {
            if (auth('portal')->check()) {
                return route('portal.dashboard');
            }

            return route('dashboard');
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
