<?php

namespace App\Entities\States\Import;

use App\Entities\Enums\ImportStatus;
use App\Entities\Import;

/**
 * Represents the "Processing" state of an import process.
 *
 * In this state, the import is actively being processed.
 * It can transition to "Completed", "Failed", or "Cancelled".
 */
class ProcessingState implements ImportState
{
    /**
     * Not allowed in this state. The import is already processing.
     *
     * @param Import $import
     * @throws \DomainException
     */
    public function startProcessing(Import $import): void
    {
        throw new \DomainException("Import is already in processing state.");
    }

    /**
     * Transition the import to the "Completed" state.
     *
     * @param Import $import
     */
    public function complete(Import $import): void
    {
        $import->changeState(new CompletedState());
    }

    /**
     * Transition the import to the "Failed" state with a given error message.
     *
     * @param Import $import
     * @param string $error
     */
    public function fail(Import $import, string $error): void
    {
        $import->changeState(new FailedState($error));
    }

    /**
     * Transition the import to the "Cancelled" state.
     *
     * @param Import $import
     */
    public function cancel(Import $import): void
    {
        $import->changeState(new CancelledState());
    }

    /**
     * Get the status code for the "Processing" state.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return ImportStatus::PROCESSING->value;
    }

    /**
     * Get the human-readable status label for the "Processing" state.
     *
     * @return string
     */
    public function getHumanStatus(): string
    {
        return ImportStatus::PROCESSING->withMeta()['description'];
    }
}
