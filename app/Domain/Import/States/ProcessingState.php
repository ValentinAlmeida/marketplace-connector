<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;
use Carbon\Carbon;

class ProcessingState implements ImportState
{
    public function startProcessing(Import $import): void
    {
        throw new \DomainException("Importação já está em processamento");
    }

    public function complete(Import $import): void
    {
        $import->changeState(new CompletedState());
    }

    public function fail(Import $import, string $error): void
    {
        $import->changeState(new FailedState($error));
    }

    public function cancel(Import $import): void
    {
        $import->changeState(new CancelledState());
    }

    public function getStatus(): string
    {
        return ImportStatus::PROCESSING->value;
    }

    public function getHumanStatus(): string
    {
        return ImportStatus::PROCESSING->withMeta()['description'];
    }
}