<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\OfferIdsFetched;
use App\Domain\Import\Events\OffersFetched;
use App\Domain\Import\Services\OffersFetcherInterface;

class FetchOffersListener
{
    public function __construct(
        private OffersFetcherInterface $offersFetcher
    ) {}

    public function handle(OfferIdsFetched $event): void
    {
        $offers = $this->offersFetcher->fetch($event->offerIds);
        event(new OffersFetched($event->importId, $offers));
    }
}
