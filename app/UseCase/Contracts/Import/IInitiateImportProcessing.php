<?php

namespace App\UseCase\Contracts\Import;

/**
 * Interface IInitiateImportProcessing
 *
 * Defines the contract for a use case responsible for initiating the main
 * processing workflow for a specific import.
 */
interface IInitiateImportProcessing
{
    /**
     * Executes the initiation of the import processing workflow.
     *
     * Implementations of this method are expected to start the overall import process,
     * which may involve orchestrating various sub-tasks like fetching offer IDs,
     * details, and processing them.
     *
     * @param int $importId The ID of the import for which processing should be initiated.
     * @return void
     */
    public function execute(int $importId): void;
}