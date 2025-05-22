<?php

namespace App\UseCase\Import;

use App\Jobs\Import\ProcessImportJob;
use App\UseCase\Contracts\Import\IProcess;

/**
 * Class Process
 *
 * Use case responsible for dispatching a job to handle the processing of an import.
 * This serves as an entry point to queue the main import processing task.
 */
class Process implements IProcess
{
    /**
     * Executes the action of dispatching an import processing job.
     *
     * Dispatches a ProcessImportJob to the 'imports_control' queue.
     *
     * @param int $importId The ID of the import to be processed.
     * @return void
     */
    public function execute(int $importId): void
    {
        ProcessImportJob::dispatch($importId)->onQueue('imports_control');
    }
}