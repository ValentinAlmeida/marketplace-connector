<?php

namespace App\Entities;

use App\Entities\ValueObjects\Identifier;

/**
 * Abstract base class for domain entities.
 * Provides a common implementation for handling entity identifiers.
 */
abstract class BaseEntity
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
