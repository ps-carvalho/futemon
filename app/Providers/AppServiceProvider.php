<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\IPlayerRepository;
use App\Contracts\Services\IImportService;
use App\Contracts\Services\IPlayerService;
use App\Repositories\PlayerRepository;
use App\Services\ImportDataService;
use App\Services\PlayersService;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IPlayerRepository::class, PlayerRepository::class);
        $this->app->bind(IPlayerService::class, PlayersService::class);
        $this->app->bind(IImportService::class, ImportDataService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
