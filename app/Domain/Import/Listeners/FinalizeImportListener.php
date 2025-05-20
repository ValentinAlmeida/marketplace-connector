<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\OffersSentToHub;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\States\CompletedState;
use Illuminate\Support\Carbon;

class FinalizeImportListener
{
    /**
     * Create a new listener instance.
     *
     * @param ImportServiceInterface $importService Service to manage import entities.
     */
    public function __construct(
        private ImportServiceInterface $importService
    ) {}

    /**
     * Handle the OffersSentToHub event.
     *
     * This listener marks the import as completed and sets the completion timestamp.
     *
     * @param OffersSentToHub $event The event signaling that offers were successfully sent to the hub.
     * @return void
     */
    public function handle(OffersSentToHub $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $import->changeState(new CompletedState());
        $import->setCompletedAt(Carbon::now());

        $this->importService->updateImport($import);
    }
}
