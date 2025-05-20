<?php

namespace App\Domain\Shared\Entity;

use App\Domain\Shared\ValueObjects\Identifier;

/**
 * Abstract base class for domain entities.
 * Provides a common implementation for handling entity identifiers.
 */
abstract class AbstractEntity
{
    /**
     * Creates a new instance of the entity.
     *
     * @param Identifier|null $id The unique identifier of the entity.
     */
    public function __construct(
        protected readonly ?Identifier $id
    ) {}

    /**
     * Returns the unique identifier of the entity.
     *
     * @return Identifier|null
     */
    public function getIdentifier(): ?Identifier
    {
        return $this->id;
    }
}
