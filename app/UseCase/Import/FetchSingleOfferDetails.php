<?php

namespace App\UseCase\Import;

use App\Events\Import\OfferDetailsFetchedForImport;
use App\Events\Import\OfferProcessingFailed;
use App\UseCase\Contracts\Gateways\IOfferFetcher;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Contracts\Import\IFetchSingleOfferDetails;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class FetchSingleOfferDetails
 *
 * Use case responsible for fetching details for a single offer within an import process.
 * It handles cases where the import or offer details are not found, or if the import is already finalized,
 * by dispatching appropriate events (OfferDetailsFetchedForImport on success, OfferProcessingFailed on failure).
 */
class FetchSingleOfferDetails implements IFetchSingleOfferDetails
{
    /**
     * FetchSingleOfferDetails constructor.
     *
     * @param IImportProcessor $importService Service for interacting with import data.
     * @param IOfferFetcher $fetcher Service for fetching specific offer details.
     */
    public function __construct(
        private IImportProcessor $importService,
        private IOfferFetcher $fetcher
    ) {}

    /**
     * Executes the process of fetching details for a single offer.
     *
     * If the import is not found or is already finalized, or if offer details cannot be fetched,
     * an OfferProcessingFailed event is dispatched. On successful fetching of details,
     * an OfferDetailsFetchedForImport event is dispatched.
     *
     * @param int $importId The ID of the import process.
     * @param int $offerId The ID of the specific offer for which to fetch details.
     * @return void
     */
    public function execute(int $importId, int $offerId): void
    {
        Log::info("FetchSingleOfferDetailsUseCase: Started for importId {$importId}, offerId {$offerId}");
        try {
            $import = $this->importService->findImport($importId);
            if (!$import) {
                Log::error("FetchSingleOfferDetailsUseCase: Import {$importId} not found.");
                OfferProcessingFailed::dispatch($importId, $offerId, 'fetch_details_setup', "Import {$importId} not found.");
                return;
            }
            if ($import->getProps()->status->isFinal()) {
                Log::warning("FetchSingleOfferDetailsUseCase: Import {$importId} already finalized. Aborting for offerId {$offerId}.");
                return;
            }

            $offersData = $this->fetcher->fetch([$offerId]);

            if (empty($offersData) || !isset($offersData[0])) {
                throw new \RuntimeException("Details not found for offerId {$offerId}.");
            }
            
            OfferDetailsFetchedForImport::dispatch($importId, $offerId, $offersData[0]);
            Log::info("FetchSingleOfferDetailsUseCase: Success for offerId {$offerId}. Event OfferDetailsFetchedForImport dispatched.");

        } catch (Throwable $e) {
            Log::error("FetchSingleOfferDetailsUseCase: Failure for importId {$importId}, offerId {$offerId}. Error: {$e->getMessage()}");
            OfferProcessingFailed::dispatch($importId, $offerId, 'fetch_details', substr($e->getMessage(), 0, 250));
        }
    }
}