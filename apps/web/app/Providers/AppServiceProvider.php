<?php

namespace App\Providers;

use App\Support\LegalConfigValidator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        app(LegalConfigValidator::class)->validate(
            environment: $this->app->environment(),
            legal: config('legal'),
        );
    }
}
