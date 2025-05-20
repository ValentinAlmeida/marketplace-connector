<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Jobs\ImportOffersJob;

/**
 * Class ScheduleImportJobUseCase
 *
 * Handles scheduling the import job for execution at the specified time.
 */
class ScheduleImportJobUseCase
{
    /**
     * Dispatches the ImportOffersJob to be executed after a given delay.
     *
     * The delay is based on the scheduledAt property of the Import entity.
     *
     * @param Import $import The import entity containing scheduling information
     */
    public function execute(Import $import): void
    {
        ImportOffersJob::dispatch($import->getIdentifier()->value())
            ->delay($import->getProps()->scheduledAt);
    }
}
