<?php

namespace App\Providers;

use App\Services\User\Repository\Eloquent\UserRepository;
use App\Services\User\Repository\UserRepositoryInterface;
use App\Services\User\TelegramService;
use App\Services\User\TrelloService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
