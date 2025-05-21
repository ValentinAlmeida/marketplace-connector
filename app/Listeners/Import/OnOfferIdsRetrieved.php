<?php

namespace App\Listeners\Import;

use App\Events\Import\OfferIdsRetrieved;
use App\Events\Import\OffersRetrieved;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Import\Support\HandlesImportFailures;

class OnOfferIdsRetrieved
{
    use HandlesImportFailures;
    /**
     * Create a new listener instance.
     *
     * @param OffersFetcherInterface $offersFetcher Service responsible for fetching offer data.
     */
    public function __construct(
        private IOfferFetcher $offersFetcher
    ) {}

    /**
     * Handle the OfferIdsFetched event.
     *
     * This listener fetches offer details using the provided offer IDs
     * and dispatches the OffersFetched event with the retrieved offers.
     *
     * @param OfferIdsFetched $event The event containing the import ID and offer IDs.
     * @return void
     */
    public function handle(OfferIdsRetrieved $event): void
    {
        $this->executeSafely($event->importId, function () use ($event) {
            $offers = $this->offersFetcher->fetch($event->offerIds);
            event(new OffersRetrieved($event->importId, $offers));
        });
    }
}
