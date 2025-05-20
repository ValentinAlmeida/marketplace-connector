<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Domain\Import\Events\ImportStarted;
use App\Domain\Import\Events\OfferIdsFetched;
use App\Domain\Import\Events\OffersFetched;
use App\Domain\Import\Events\OffersSentToHub;

use App\Domain\Import\Listeners\FetchOfferIdsListener;
use App\Domain\Import\Listeners\FetchOffersListener;
use App\Domain\Import\Listeners\SendOffersToHubListener;
use App\Domain\Import\Listeners\FinalizeImportListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ImportStarted::class => [
            FetchOfferIdsListener::class,
        ],
        OfferIdsFetched::class => [
            FetchOffersListener::class,
        ],
        OffersFetched::class => [
            SendOffersToHubListener::class,
        ],
        OffersSentToHub::class => [
            FinalizeImportListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // ou true se quiser usar auto-discovery em vez de mapear manualmente
    }
}
