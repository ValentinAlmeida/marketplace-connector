<?php

namespace App\Entities\States\Import;

use App\Entities\Enums\ImportStatus;
use App\Entities\Import;

/**
 * Represents the "failed" state of an import process.
 *
 * In this state, the import process has encountered an error and failed.
 * From this state, it is possible to retry the import (transition to processing),
 * fail again with a new error, or cancel the import entirely.
 */
class FailedState implements ImportState
{
    /**
     * @param string $error The error message that caused the import to fail.
     */
    public function __construct(private readonly string $error) {}

    /**
     * Transition the import back to the processing state to retry the import.
     *
     * @param Import $import
     */
    public function startProcessing(Import $import): void
    {
        $import->changeState(new ProcessingState());
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a failed import cannot be directly marked as completed.
     */
    public function complete(Import $import): void
    {
        throw new \DomainException("A failed import cannot be directly marked as completed");
    }

    /**
     * Updates the failed state with a new error message.
     *
     * @param Import $import
     * @param string $error
     */
    public function fail(Import $import, string $error): void
    {
        $import->changeState(new FailedState($error));
    }

    /**
     * Transition the import to the cancelled state.
     *
     * @param Import $import
     */
    public function cancel(Import $import): void
    {
        $import->changeState(new CancelledState());
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return ImportStatus::FAILED->value;
    }

    /**
     * @inheritDoc
     */
    public function getHumanStatus(): string
    {
        return ImportStatus::FAILED->withMeta()['description'];
    }

    /**
     * Returns the error message that caused the failure.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
