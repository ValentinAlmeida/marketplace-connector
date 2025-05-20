<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;

/**
 * Represents the "completed" state of an import process.
 *
 * In this state, the import process has been successfully finished,
 * and no further state transitions such as processing, failing,
 * or cancelling are allowed. Attempts to perform such actions
 * will throw a domain exception.
 */
class CompletedState implements ImportState
{
    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a completed import cannot be reprocessed.
     */
    public function startProcessing(Import $import): void
    {
        throw new \DomainException("Import has already been completed");
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because the import is already completed.
     */
    public function complete(Import $import): void
    {
        throw new \DomainException("Import is already completed");
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a completed import cannot fail.
     */
    public function fail(Import $import, string $error): void
    {
        throw new \DomainException("Completed import cannot fail");
    }

    /**
     * @inheritDoc
     *
     * @throws \DomainException Always, because a completed import cannot be cancelled.
     */
    public function cancel(Import $import): void
    {
        throw new \DomainException("Completed import cannot be cancelled");
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return ImportStatus::COMPLETED->value;
    }

    /**
     * @inheritDoc
     */
    public function getHumanStatus(): string
    {
        return ImportStatus::COMPLETED->withMeta()['description'];
    }
}
