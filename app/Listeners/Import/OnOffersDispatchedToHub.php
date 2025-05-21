<?php

namespace App\Listeners\Import;

use App\Entities\States\Import\CompletedState;
use App\Events\Import\OffersDispatchedToHub;
use App\UseCase\Contracts\Import\IImportProcessor;
use Illuminate\Support\Carbon;

class OnOffersDispatchedToHub
{
    /**
     * Create a new listener instance.
     *
     * @param ImportServiceInterface $importService Service to manage import entities.
     */
    public function __construct(
        private IImportProcessor $importService
    ) {}

    /**
     * Handle the OffersSentToHub event.
     *
     * This listener marks the import as completed and sets the completion timestamp.
     *
     * @param OffersSentToHub $event The event signaling that offers were successfully sent to the hub.
     * @return void
     */
    public function handle(OffersDispatchedToHub $event): void
    {
        $import = $this->importService->findImport($event->importId);
        try {
            $import->changeState(new CompletedState());
            $import->setCompletedAt(Carbon::now());

            $this->importService->updateImport($import);
        } catch (\Throwable $e) {
            $import->fail($e->getMessage());
            $this->importService->updateImport($import);
        }
    }
}
