<?php

namespace App\UseCase\Contracts\Import;

/**
 * Interface IProcess
 *
 * Defines the contract for a use case responsible for initiating or handling
 * the processing of an existing import.
 */
interface IProcess
{
    /**
     * Executes the import processing logic.
     *
     * @param int $importId The ID of the import to be processed.
     * @return void
     */
    public function execute(int $importId): void;
}