<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\OffersFetched;
use App\Domain\Import\Events\OffersSentToHub;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\Services\OfferHubSenderInterface;

class SendOffersToHubListener
{
    /**
     * Create a new listener instance.
     *
     * @param ImportServiceInterface $importService Service to manage import entities.
     * @param OfferHubSenderInterface $hubSender Service responsible for sending offers to the external hub.
     */
    public function __construct(
        private ImportServiceInterface $importService,
        private OfferHubSenderInterface $hubSender
    ) {}

    /**
     * Handle the OffersFetched event.
     *
     * This listener sends the fetched offers to the hub, updates the import state,
     * and dispatches a new event indicating the offers were sent.
     *
     * @param OffersFetched $event The event containing the offers to be sent.
     * @return void
     */
    public function handle(OffersFetched $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $this->hubSender->send($event->offers, $import);
        $this->importService->updateImport($import);

        event(new OffersSentToHub($event->importId));
    }
}
