<?php

namespace App\Providers;

use App\Domain\Import\Config\ImportConfig;
use App\Domain\Import\Services\OfferHubSenderInterface;
use App\Domain\Import\Services\OffersFetcherInterface;
use App\Domain\Import\Services\PaginatedOfferFetcherInterface;
use App\Infrastructure\Import\Services\HttpOfferHubSender;
use App\Infrastructure\Import\Services\HttpOffersFetcher;
use App\Infrastructure\Import\Services\HttpPaginatedOfferFetcher;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ImportConfig::class, function () {
            return new ImportConfig(
                env('IMPORTER_URL', 'http://localhost:3000')
            );
        });

        $this->app->singleton(OfferHubSenderInterface::class, HttpOfferHubSender::class);
        $this->app->singleton(OffersFetcherInterface::class, HttpOffersFetcher::class);
        $this->app->singleton(PaginatedOfferFetcherInterface::class, HttpPaginatedOfferFetcher::class);
    }
}