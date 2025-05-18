<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;

class FailedState implements ImportState
{
    public function __construct(private readonly string $error) {}

    public function startProcessing(Import $import): void
    {
        $import->changeState(new ProcessingState());
    }

    public function complete(Import $import): void
    {
        throw new \DomainException("Importação falhada não pode ser completada diretamente");
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
        return ImportStatus::FAILED->value;
    }

    public function getHumanStatus(): string
    {
        return ImportStatus::FAILED->withMeta()['description'];
    }

    public function getError(): string
    {
        return $this->error;
    }
}