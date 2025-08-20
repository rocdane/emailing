<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\EmailParsingService;
use App\Services\IEmailParsingService;
use App\Services\EmailCampaignService;
use App\Services\IEmailCampaignService;

use App\Jobs\MailingProgress;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IEmailParsingService::class, EmailParsingService::class);

        $this->app->bind(IEmailCampaignService::class, EmailCampaignService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
    }
}
