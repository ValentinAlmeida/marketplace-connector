<?php

namespace App\Domain\Import\Factories;

use App\Domain\Import\Enums\ImportStatus;
use App\Domain\Import\States\CancelledState;
use App\Domain\Import\States\CompletedState;
use App\Domain\Import\States\FailedState;
use App\Domain\Import\States\PendingState;
use App\Domain\Import\States\ProcessingState;
use App\Domain\Import\States\ImportState;

class ImportStateFactory
{
    /**
     * Creates an ImportState instance based on the current status and optional error message.
     *
     * @param string $status The import status value.
     * @param string|null $error Optional error message used when creating a FailedState.
     * 
     * @return ImportState The corresponding state object for the given status.
     *
     * @throws \InvalidArgumentException If the provided status is invalid.
     */
    public static function make(string $status, ?string $error = null): ImportState
    {
        return match ($status) {
            ImportStatus::PENDING->value => new PendingState(),
            ImportStatus::PROCESSING->value => new ProcessingState(),
            ImportStatus::COMPLETED->value => new CompletedState(),
            ImportStatus::FAILED->value => new FailedState($error ?? 'Unknown error'),
            ImportStatus::CANCELLED->value => new CancelledState(),
            default => throw new \InvalidArgumentException("Invalid import status: $status"),
        };
    }
}
