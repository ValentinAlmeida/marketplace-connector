<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;

/**
 * Interface defining the contract for import states.
 *
 * Each state encapsulates behavior specific to that state in the import lifecycle,
 * including allowed transitions and status representation.
 */
interface ImportState
{
    /**
     * Initiates processing of the import.
     *
     * @param Import $import
     * @throws \DomainException If the current state does not allow processing.
     */
    public function startProcessing(Import $import): void;

    /**
     * Marks the import as completed.
     *
     * @param Import $import
     * @throws \DomainException If the current state does not allow completion.
     */
    public function complete(Import $import): void;

    /**
     * Marks the import as failed and records an error message.
     *
     * @param Import $import
     * @param string $error
     * @throws \DomainException If the current state does not allow failure.
     */
    public function fail(Import $import, string $error): void;

    /**
     * Cancels the import.
     *
     * @param Import $import
     * @throws \DomainException If the current state does not allow cancellation.
     */
    public function cancel(Import $import): void;

    /**
     * Returns the machine-readable status value of the state.
     *
     * @return string
     */
    public function getStatus(): string;

    /**
     * Returns a human-readable description of the state.
     *
     * @return string
     */
    public function getHumanStatus(): string;
}
