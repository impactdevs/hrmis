<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Mail\MailManager;
use App\Mail\Transport\InfobipTransport;class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super-Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {

            if ($user->hasRole('HR')) {

                return true;

            }

        });

        $this->app->make(MailManager::class)->extend('infobip', function () {
            $config = config('services.infobip');
            return new InfobipTransport(
                $config['base_url'],
                $config['api_key'],
                $config['email_from']
            );
        });
    }
}
