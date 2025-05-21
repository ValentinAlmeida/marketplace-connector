<?php

namespace App\UseCase\Import\Factories;

use App\Entities\Enums\ImportStatus;
use App\Entities\States\Import\CancelledState;
use App\Entities\States\Import\CompletedState;
use App\Entities\States\Import\FailedState;
use App\Entities\States\Import\ImportState;
use App\Entities\States\Import\PendingState;
use App\Entities\States\Import\ProcessingState;

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
