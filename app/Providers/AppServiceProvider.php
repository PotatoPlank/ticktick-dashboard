<?php

namespace App\Providers;

use App\Services\TickTick\TickTickClient;
use App\Services\TickTick\TickTickSyncService;
use Carbon\CarbonImmutable;
use App\Socialite\PocketIdProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TickTickClient::class);
        $this->app->singleton(TickTickSyncService::class);

//        RateLimiter::for('ticktick-sync', function (object $job){
//           return Limit::perMinute(1);
//        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Socialite::extend('pocketid', function () {
            $config = config('services.pocketid');

            return Socialite::buildProvider(PocketIdProvider::class, $config);
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
