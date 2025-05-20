<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;

/**
 * Represents the "cancelled" state of an import process.
 *
 * In this state, no further actions such as processing, completing,
 * or failing are allowed. Any such attempts will result in a domain exception.
 */
class CancelledState implements ImportState
{
    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a cancelled import cannot be reprocessed.
     */
    public function startProcessing(Import $import): void
    {
        throw new \DomainException("Cancelled import cannot be reprocessed");
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a cancelled import cannot be completed.
     */
    public function complete(Import $import): void
    {
        throw new \DomainException("Cancelled import cannot be completed");
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a cancelled import cannot fail.
     */
    public function fail(Import $import, string $error): void
    {
        throw new \DomainException("Cancelled import cannot fail");
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because the import is already cancelled.
     */
    public function cancel(Import $import): void
    {
        throw new \DomainException("Import is already cancelled");
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return ImportStatus::CANCELLED->value;
    }

    /**
     * @inheritDoc
     */
    public function getHumanStatus(): string
    {
        return ImportStatus::CANCELLED->withMeta()['description'];
    }
}
