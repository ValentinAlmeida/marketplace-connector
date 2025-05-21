<?php

namespace App\Listeners\Import;

use App\Events\Import\OffersDispatchedToHub;
use App\Events\Import\OffersRetrieved;
use App\UseCase\Contracts\Gateways\IOfferSender;
use App\UseCase\Contracts\Import\IImportProcessor;

class OnOffersRetrieved
{
    /**
     * Create a new listener instance.
     *
     * @param ImportServiceInterface $importService Service to manage import entities.
     * @param OfferHubSenderInterface $hubSender Service responsible for sending offers to the external hub.
     */
    public function __construct(
        private IImportProcessor $importService,
        private IOfferSender $hubSender
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
    public function handle(OffersRetrieved $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $this->hubSender->send($event->offers, $import);
        $this->importService->updateImport($import);

        event(new OffersDispatchedToHub($event->importId));
    }
}
