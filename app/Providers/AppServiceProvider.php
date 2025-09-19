<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Mail\MailManager;
use App\Mail\Transport\InfobipTransport;
use App\Models\Appraisal;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
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

         Route::bind('appraisal', function ($value) {
            return Appraisal::where('appraisal_id', $value)->firstOrFail();
        });

        parent::boot();
        // Implicitly grant certain roles permission for appraisal-related actions
        Gate::before(function ($user, $ability) {
            // Grant HR users all permissions
            if ($user->hasRole('HR')) {
                return true;
            }
            
            // Grant Executive Secretary users permission for appraisal approvals
            if ($user->hasRole('Executive Secretary') && str_contains($ability, 'appraisal')) {
                return true;
            }
            
            // Grant Head of Division users permission for appraisal approvals
            if ($user->hasRole('Head of Division') && str_contains($ability, 'appraisal')) {
                return true;
            }
        });

        $this->app->make(MailManager::class)->extend('infobip', function () {
            $config = config('services.infobip');
            return new InfobipTransport(
                $config['base_url'],
                $config['api_key'],
                $config['email_from'],
                $config['name'],
            );
        });



    }
}
