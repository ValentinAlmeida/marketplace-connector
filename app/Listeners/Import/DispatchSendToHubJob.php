<?php

namespace App\Listeners\Import;

use App\Events\Import\OfferDetailsFetchedForImport;
use App\Jobs\Import\SendOfferToHubJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class DispatchSendToHubJob
 *
 * Listens for the OfferDetailsFetchedForImport event and dispatches
 * a SendOfferToHubJob to the 'imports_send' queue with the fetched offer data.
 */
class DispatchSendToHubJob implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * Dispatches a SendOfferToHubJob with the details from the OfferDetailsFetchedForImport event.
     *
     * @param OfferDetailsFetchedForImport $event The event containing the import ID, offer ID, and fetched offer data.
     * @return void
     */
    public function handle(OfferDetailsFetchedForImport $event): void
    {
        Log::info("Listener DispatchSendToHubJob: importId {$event->importId}, offerId {$event->offerId}.");
        SendOfferToHubJob::dispatch($event->importId, $event->offerId, $event->offerData)
            ->onQueue('imports_send');
        Log::info("Listener DispatchSendToHubJob: SendOfferToHubJob job dispatched for importId {$event->importId}, offerId {$event->offerId}.");
    }
}