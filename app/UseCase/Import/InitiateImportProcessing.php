<?php

namespace App\UseCase\Import;

use App\Entities\States\Import\ProcessingState;
use App\Jobs\Import\FetchOfferIdsJob;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Contracts\Import\IInitiateImportProcessing;
use Illuminate\Support\Facades\Log;
use App\Entities\Enums\ImportStatus;

/**
 * Class InitiateImportProcessing
 *
 * Use case responsible for starting the processing of an import.
 * It updates the import's state to 'processing', sets the start time,
 * and dispatches a job to fetch associated offer IDs.
 */
class InitiateImportProcessing implements IInitiateImportProcessing
{
    /**
     * InitiateImportProcessing constructor.
     *
     * @param IImportProcessor $importService Service for interacting with import data.
     */
    public function __construct(private IImportProcessor $importService) {}

    /**
     * Executes the import initiation process.
     *
     * Finds the import, and if it's not already in a final or processing state,
     * changes its state to Processing, sets its started_at time, updates it,
     * and dispatches a FetchOfferIdsJob.
     *
     * @param int $importId The ID of the import to initiate.
     * @return void
     */
    public function execute(int $importId): void
    {
        $import = $this->importService->findImport($importId);

        if ($import->getProps()->status->isFinal() || $import->getProps()->status === ImportStatus::PROCESSING) {
            Log::info("InitiateImportProcessingUseCase: Import {$importId} already finalized or processing. No action taken.");
            return;
        }
        
        $import->changeState(new ProcessingState());
        $import->setStartedAt(now());
        $this->importService->updateImport($import);

        FetchOfferIdsJob::dispatch($importId)->onQueue('imports_ids');
        Log::info("InitiateImportProcessingUseCase: FetchOfferIdsJob dispatched for importId {$importId}.");
    }
}