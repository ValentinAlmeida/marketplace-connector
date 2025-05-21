<?php

namespace App\UseCase\Import;

use App\Entities\Import;
use App\Jobs\Import\ProcessOffers;
use App\UseCase\Contracts\Import\ISchedule;

/**
 * Class ScheduleImportJobUseCase
 *
 * Handles scheduling the import job for execution at the specified time.
 */
class Schedule implements ISchedule
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
        ProcessOffers::dispatch($import->getIdentifier()->value())
            ->delay($import->getProps()->scheduledAt);
    }
}
