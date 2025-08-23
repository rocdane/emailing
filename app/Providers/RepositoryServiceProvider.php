<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\App;

use App\Services\EmailCampaignService;
use App\Services\EmailParsingService;
use App\Repositories\SuscriberRepository;
use App\Repositories\EmailRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        App::bind(SuscriberRepository::class, function ($app) {
            return new SuscriberRepository();
        });

        App::bind(EmailRepository::class, function ($app) {
            return new EmailRepository();
        });

        App::bind(EmailParsingService::class, function (Application $app) {
            return new EmailParsingService();
        });

        App::bind(EmailCampaignService::class, function (Application $app) {
            return new EmailCampaignService($app->make(EmailParsingService::class), $app->make(SuscriberRepository::class), $app->make(EmailRepository::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
