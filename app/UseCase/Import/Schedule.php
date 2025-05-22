<?php

namespace App\UseCase\Import;

use App\Entities\Import;
use App\Jobs\Import\ProcessImportJob;
use App\UseCase\Contracts\Import\ISchedule;

/**
 * Class Schedule
 *
 * Use case responsible for scheduling an import processing job.
 * It dispatches the job with a delay corresponding to the import's scheduled time.
 */
class Schedule implements ISchedule
{
    /**
     * Executes the scheduling of an import processing job.
     *
     * Dispatches a ProcessImportJob to the 'imports_control' queue,
     * delayed until the scheduledAt time specified in the Import entity.
     *
     * @param Import $import The Import entity containing the scheduling details and ID.
     * @return void
     */
    public function execute(Import $import): void
    {
        ProcessImportJob::dispatch($import->getIdentifier()->value())
            ->delay($import->getProps()->scheduledAt)
            ->onQueue('imports_control');
    }
}