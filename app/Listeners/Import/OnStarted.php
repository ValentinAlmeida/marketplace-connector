<?php

namespace App\Listeners\Import;

use App\Events\Import\OfferIdsRetrieved;
use App\Events\Import\Started;
use App\UseCase\Contracts\Gateways\IPaginatedOfferFetcher;
use App\UseCase\Contracts\Import\IImportProcessor;

class OnStarted
{
    /**
     * Create a new listener instance.
     *
     * @param ImportServiceInterface $importService Service to fetch and update imports.
     * @param PaginatedOfferFetcherInterface $paginatedFetcher Service to fetch offer IDs from external source.
     */
    public function __construct(
        private IImportProcessor $importService,
        private IPaginatedOfferFetcher $paginatedFetcher
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
    public function handle(Started $event): void
    {
        $import = $this->importService->findImport($event->importId);
        $offerIds = $this->paginatedFetcher->fetch();

        if (empty($offerIds)) {
            $import->fail('No offers available at the source URL');
            $this->importService->updateImport($import);
            return;
        }
        
        event(new OfferIdsRetrieved($import->getIdentifier()->value(), $offerIds));
    }
}
