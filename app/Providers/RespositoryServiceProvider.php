<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\EmailRepository;
use App\Repositories\EmailRepositoryInterface;
use App\Repositories\SuscriberRepository;
use App\Repositories\SuscriberRepositoryInterface;


class RespositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmailRepositoryInterface::class, EmailRepository::class);
        
        $this->app->bind(SuscriberRepositoryInterface::class, SuscriberRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
