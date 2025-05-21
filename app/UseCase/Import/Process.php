<?php

namespace App\UseCase\Import;

use App\Events\Import\Started;
use App\UseCase\Contracts\Import\IProcess;

/**
 * Use case responsible for initiating the import process.
 *
 * This class triggers the initial event that starts the import flow.
 */
class Process implements IProcess
{
    /**
     * Execute the import process by dispatching the ImportStarted event.
     *
     * @param int $importId The identifier of the import to be processed.
     */
    public function execute(int $importId): void
    {
        event(new Started($importId));
    }
}
