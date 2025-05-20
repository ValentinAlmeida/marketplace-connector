<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\OfferIdsFetched;
use App\Domain\Import\Events\OffersFetched;
use App\Domain\Import\Services\OffersFetcherInterface;

class FetchOffersListener
{
    /**
     * Create a new listener instance.
     *
     * @param OffersFetcherInterface $offersFetcher Service responsible for fetching offer data.
     */
    public function __construct(
        private OffersFetcherInterface $offersFetcher
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
    public function handle(OfferIdsFetched $event): void
    {
        $offers = $this->offersFetcher->fetch($event->offerIds);
        event(new OffersFetched($event->importId, $offers));
    }
}
