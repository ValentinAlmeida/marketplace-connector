<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Events\ImportStarted;

/**
 * Use case responsible for initiating the import process.
 *
 * This class triggers the initial event that starts the import flow.
 */
class ProcessImportUseCase
{
    /**
     * Execute the import process by dispatching the ImportStarted event.
     *
     * @param int $importId The identifier of the import to be processed.
     */
    public function execute(int $importId): void
    {
        event(new ImportStarted($importId));
    }
}
