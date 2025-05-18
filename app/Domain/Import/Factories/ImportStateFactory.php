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
    public static function make(string $status, ?string $error = null): ImportState
    {
        return match ($status) {
            ImportStatus::PENDING->value => new PendingState(),
            ImportStatus::PROCESSING->value => new ProcessingState(),
            ImportStatus::COMPLETED->value => new CompletedState(),
            ImportStatus::FAILED->value => new FailedState($error ?? 'Erro desconhecido'),
            ImportStatus::CANCELLED->value => new CancelledState(),
            default => throw new \InvalidArgumentException("Estado de importação inválido: $status")
        };
    }
}