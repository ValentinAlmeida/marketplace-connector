<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;
use Carbon\Carbon;

class PendingState implements ImportState
{
    public function startProcessing(Import $import): void
    {
        $import->changeState(new ProcessingState());
        $import->setStartedAt(Carbon::now());
    }

    public function complete(Import $import): void
    {
        throw new \DomainException("Importação pendente não pode ser completada diretamente");
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
        return ImportStatus::PENDING->value;
    }

    public function getHumanStatus(): string
    {
        return ImportStatus::PENDING->withMeta()['description'];
    }
}