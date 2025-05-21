<?php

namespace App\Providers;

use App\UseCase\Import\Config\ImportConfig;

use App\Gateways\Offer\HttpFetcher;
use App\Gateways\Offer\HttpSender;
use App\Gateways\Offer\PaginatedHttpFetcher;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Contracts\Gateways\IOfferSender;
use App\UseCase\Contracts\Gateways\IPaginatedOfferFetcher;
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

        $this->app->singleton(IOfferSender::class, HttpSender::class);
        $this->app->singleton(IOfferFetcher::class, HttpFetcher::class);
        $this->app->singleton(IPaginatedOfferFetcher::class, PaginatedHttpFetcher::class);
    }
}