<?php

namespace App\Listeners\Import;

use App\Entities\States\Import\CompletedState;
use App\Entities\States\Import\FailedState;
use App\Events\Import\OfferProcessingFailed;
use App\Events\Import\OfferSuccessfullySentToHub;
use App\UseCase\Contracts\Import\IImportProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdateImportProgress
 *
 * Listens for offer processing events (success or failure) to update the overall import progress.
 * It can also finalize an import if all its items have been processed.
 */
class UpdateImportProgress implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @param IImportProcessor $importService Service to handle import data operations.
     */
    public function __construct(private IImportProcessor $importService) {}

    /**
     * Handle the incoming offer processing event.
     *
     * Updates the import's progress based on whether an offer was successfully processed or failed.
     * After updating, it checks if the import can be finalized.
     *
     * @param OfferSuccessfullySentToHub|OfferProcessingFailed $event The event indicating offer processing outcome.
     * @return void
     */
    public function handle(OfferSuccessfullySentToHub|OfferProcessingFailed $event): void
    {
        Log::info("Listener UpdateImportProgress: importId {$event->importId}. Event: " . get_class($event));
        $import = $this->importService->findImport($event->importId);

        if (!$import || $import->getProps()->status->isFinal()) {
            Log::warning("Listener UpdateImportProgress: Import {$event->importId} not found or already finalized.");
            return;
        }

        if ($event instanceof OfferSuccessfullySentToHub) {
            $import->incrementProcessedItems();
            Log::info("Listener UpdateImportProgress: Processed OK for importId {$event->importId}, offerId {$event->originalOfferId}.");
        } elseif ($event instanceof OfferProcessingFailed) {
            $import->incrementFailedItems();
            $import->addErrorDetail(
                $event->failureStage . '_offer_' . $event->originalOfferId,
                $event->reason
            );
            Log::error("Listener UpdateImportProgress: Failure for importId {$event->importId}, offerId {$event->originalOfferId}. Stage: {$event->failureStage}, Reason: {$event->reason}");
        }

        $this->importService->updateImport($import);
        $this->checkAndFinalizeImport($event->importId);
    }

    /**
     * Checks if the import process can be finalized based on its item counts.
     * If all items are processed (either successfully or failed), it updates the import to a final state.
     *
     * @param int $importId The ID of the import to check and potentially finalize.
     * @return void
     */
    private function checkAndFinalizeImport(int $importId): void
    {
        $import = $this->importService->findImport($importId);

        $props = $import->getProps();

        if ($props->status->isFinal() || $props->totalItems <= 0) {
            return;
        }

        if (($props->processedItems + $props->failedItems) >= $props->totalItems) {
            $finalized = false;
            if ($props->failedItems === $props->totalItems) {
                Log::warning("Listener UpdateImportProgress (checkAndFinalizeImport): Import {$importId} FAILED COMPLETELY. P:{$props->processedItems}, F:{$props->failedItems}, T:{$props->totalItems}");
                $import->fail("All {$props->totalItems} offers failed during processing.");
                $finalized = true;
            } else {
                Log::info("Listener UpdateImportProgress (checkAndFinalizeImport): Import {$importId} completing (partial or total success). P:{$props->processedItems}, F:{$props->failedItems}, T:{$props->totalItems}");
                $import->changeState(new CompletedState());
                $import->setCompletedAt(now());
                $finalized = true;
            }

            if ($finalized) {
                $this->importService->updateImport($import);
                Log::info("Listener UpdateImportProgress (checkAndFinalizeImport): Import {$importId} updated to final state: {$import->getProps()->status->value}.");
            }
        }
    }
}