<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\Import\OfferIdsRetrieved;
use App\Events\Import\OffersDispatchedToHub;
use App\Events\Import\OffersRetrieved;
use App\Events\Import\Started;

use App\Listeners\Import\OnOfferIdsRetrieved;
use App\Listeners\Import\OnOffersDispatchedToHub;
use App\Listeners\Import\OnOffersRetrieved;
use App\Listeners\Import\OnStarted;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Started::class => [
            OnStarted::class,
        ],
        OfferIdsRetrieved::class => [
            OnOfferIdsRetrieved::class,
        ],
        OffersRetrieved::class => [
            OnOffersRetrieved::class,
        ],
        OffersDispatchedToHub::class => [
            OnOffersDispatchedToHub::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
