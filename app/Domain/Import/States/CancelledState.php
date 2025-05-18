<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;

class CancelledState implements ImportState
{
    public function startProcessing(Import $import): void
    {
        throw new \DomainException("Importação cancelada não pode ser reprocessada");
    }

    public function complete(Import $import): void
    {
        throw new \DomainException("Importação cancelada não pode ser completada");
    }

    public function fail(Import $import, string $error): void
    {
        throw new \DomainException("Importação cancelada não pode falhar");
    }

    public function cancel(Import $import): void
    {
        throw new \DomainException("Importação já está cancelada");
    }

    public function getStatus(): string
    {
        return ImportStatus::CANCELLED->value;
    }

    public function getHumanStatus(): string
    {
        return ImportStatus::CANCELLED->withMeta()['description'];
    }
}