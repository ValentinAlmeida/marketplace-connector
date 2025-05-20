<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\OffersFetched;
use App\Domain\Import\Events\OffersSentToHub;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\Services\OfferHubSenderInterface;

class SendOffersToHubListener
{
    public function __construct(
        private ImportServiceInterface $importService,
        private OfferHubSenderInterface $hubSender
    ) {}

    public function handle(OffersFetched $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $this->hubSender->send($event->offers, $import);
        $this->importService->updateImport($import);

        event(new OffersSentToHub($event->importId));
    }
}
