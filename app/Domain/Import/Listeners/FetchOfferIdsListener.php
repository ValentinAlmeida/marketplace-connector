<?php

namespace App\Domain\Import\Listeners;

use App\Domain\Import\Events\ImportStarted;
use App\Domain\Import\Events\OfferIdsFetched;
use App\Domain\Import\Services\ImportServiceInterface;
use App\Domain\Import\Services\PaginatedOfferFetcherInterface;

class FetchOfferIdsListener
{
    /**
     * Create a new listener instance.
     *
     * @param ImportServiceInterface $importService Service to fetch and update imports.
     * @param PaginatedOfferFetcherInterface $paginatedFetcher Service to fetch offer IDs from external source.
     */
    public function __construct(
        private ImportServiceInterface $importService,
        private PaginatedOfferFetcherInterface $paginatedFetcher
    ) {}

    /**
     * Handle the ImportStarted event.
     *
     * This listener fetches offer IDs from an external source. If no offers are found,
     * it marks the import as failed. Otherwise, it dispatches the OfferIdsFetched event.
     *
     * @param ImportStarted $event The event containing the import ID.
     * @return void
     */
    public function handle(ImportStarted $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $offerIds = $this->paginatedFetcher->fetch();

        if (empty($offerIds)) {
            $import->fail('No offers available at the source URL');
            $this->importService->updateImport($import);
            return;
        }
        
        event(new OfferIdsFetched($import->getIdentifier()->value(), $offerIds));
    }
}
