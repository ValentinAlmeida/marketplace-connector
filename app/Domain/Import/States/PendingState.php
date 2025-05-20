<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;
use Carbon\Carbon;

/**
 * Represents the "Pending" state of an import process.
 *
 * In this state, the import has not yet started. It can transition
 * to "Processing", "Failed", or "Cancelled", but cannot be completed directly.
 */
class PendingState implements ImportState
{
    /**
     * Transition the import to the "Processing" state and set the start timestamp.
     *
     * @param Import $import The import instance to update.
     */
    public function startProcessing(Import $import): void
    {
        $import->changeState(new ProcessingState());
        $import->setStartedAt(Carbon::now());
    }

    /**
     * Not allowed in this state. A pending import cannot be completed directly.
     *
     * @param Import $import
     * @throws \DomainException
     */
    public function complete(Import $import): void
    {
        throw new \DomainException("Pending import cannot be completed directly.");
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
     * Get the status code for the "Pending" state.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return ImportStatus::PENDING->value;
    }

    /**
     * Get the human-readable status label for the "Pending" state.
     *
     * @return string
     */
    public function getHumanStatus(): string
    {
        return ImportStatus::PENDING->withMeta()['description'];
    }
}
