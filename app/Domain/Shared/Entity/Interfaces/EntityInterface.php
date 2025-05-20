<?php

namespace App\Domain\Shared\Entity\Interfaces;

use App\Domain\Shared\ValueObjects\Identifier;

/**
 * Interface for domain entities that require a unique identifier.
 */
interface EntityInterface
{
    /**
     * Returns the unique identifier of the entity.
     *
     * @return Identifier
     */
    public function getIdentifier(): Identifier;
}
