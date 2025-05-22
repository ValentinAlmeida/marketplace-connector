<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;

/**
 * Interface ISchedule
 *
 * Defines the contract for a use case responsible for scheduling an import.
 */
interface ISchedule
{
    /**
     * Executes the import scheduling logic.
     *
     * This typically involves setting up the import to be processed at a specific time
     * or after certain conditions are met.
     *
     * @param Import $import The Import entity to be scheduled.
     * @return void
     */
    public function execute(Import $import): void;
}