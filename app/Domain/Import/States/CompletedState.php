<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;

class CompletedState implements ImportState
{
    public function startProcessing(Import $import): void
    {
        throw new \DomainException("Importação já foi completada");
    }

    public function complete(Import $import): void
    {
        throw new \DomainException("Importação já está completada");
    }

    public function fail(Import $import, string $error): void
    {
        throw new \DomainException("Importação completada não pode falhar");
    }

    public function cancel(Import $import): void
    {
        throw new \DomainException("Importação completada não pode ser cancelada");
    }

    public function getStatus(): string
    {
        return ImportStatus::COMPLETED->value;
    }

    public function getHumanStatus(): string
    {
        return ImportStatus::COMPLETED->withMeta()['description'];
    }
}