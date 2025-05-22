<?php

namespace App\Listeners\Import;

use App\Events\Import\OfferIdsRetrievedForImport;
use App\Jobs\Import\FetchOfferDetailsJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class DispatchOfferDetailJobs
 *
 * Listens for the OfferIdsRetrievedForImport event and dispatches
 * FetchOfferDetailsJob for each retrieved offer ID to the 'imports_details' queue.
 */
class DispatchOfferDetailJobs implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * Dispatches a FetchOfferDetailsJob for each offer ID received in the event.
     *
     * @param OfferIdsRetrievedForImport $event The event containing the import ID and the array of offer IDs.
     * @return void
     */
    public function handle(OfferIdsRetrievedForImport $event): void
    {
        Log::info("Listener DispatchOfferDetailJobs: importId {$event->importId}, " . count($event->offerIds) . " IDs received.");
        foreach ($event->offerIds as $offerId) {
            FetchOfferDetailsJob::dispatch($event->importId, (int)$offerId)
                ->onQueue('imports_details');
        }
        Log::info("Listener DispatchOfferDetailJobs: FetchOfferDetailsJob jobs dispatched for importId {$event->importId}.");
    }
}