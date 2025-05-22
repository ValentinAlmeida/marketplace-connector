<?php

namespace App\UseCase\Import;

use App\Events\Import\OfferIdsRetrievedForImport;
use App\UseCase\Contracts\Gateways\IPaginatedOfferFetcher;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Contracts\Import\IFetchAllOfferIds;
use Illuminate\Support\Facades\Log;
use App\Exceptions\Import\NoOffersFoundException;

/**
 * Class FetchAllOfferIds
 *
 * Use case responsible for fetching all offer IDs for a given import process
 * using a paginated fetcher. It updates the import's total item count and
 * dispatches an event with the retrieved IDs.
 */
class FetchAllOfferIds implements IFetchAllOfferIds
{
    /**
     * FetchAllOfferIds constructor.
     *
     * @param IImportProcessor $importService Service for interacting with import data.
     * @param IPaginatedOfferFetcher $fetcher Service for fetching offer IDs from a paginated source.
     */
    public function __construct(
        private IImportProcessor $importService,
        private IPaginatedOfferFetcher $fetcher
    ) {}

    /**
     * Executes the process of fetching all offer IDs for the specified import.
     *
     * Retrieves offer IDs, updates the import entity with the total count,
     * and dispatches an OfferIdsRetrievedForImport event. If no offers are found,
     * the import is marked as complete.
     *
     * @param int $importId The ID of the import for which to fetch offer IDs.
     * @return void
     * @throws \RuntimeException If the import record is not found.
     */
    public function execute(int $importId): void
    {
        Log::info("FetchAllOfferIdsUseCase: Started for importId {$importId}");
        $import = $this->importService->findImport($importId);

        $offerIds = $this->fetcher->fetch();

        if (empty($offerIds)) {
            Log::warning("FetchAllOfferIdsUseCase: No offer IDs found for importId {$importId}.");
            $import->updateProgress(0, 0);
            $import->complete();
            $this->importService->updateImport($import);
            Log::info("FetchAllOfferIdsUseCase: No offers for import {$importId}. Marked as complete.");
            return;
        }

        Log::info(count($offerIds) . " IDs found for importId {$importId}.");
        $import->updateProgress(0, count($offerIds));
        $this->importService->updateImport($import);

        OfferIdsRetrievedForImport::dispatch($importId, $offerIds);
        Log::info("FetchAllOfferIdsUseCase: Event OfferIdsRetrievedForImport dispatched for importId {$importId}");
    }
}