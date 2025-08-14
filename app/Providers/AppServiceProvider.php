<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\MailService;
use App\Jobs\MailingProgress;

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
        $this->app->bindMethod([MailingProgress::class, 'handle'], function (MailingProgress $job, Application $app) {
            return $job->handle($app->make(MailService::class));
        });
    }
}
