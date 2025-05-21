<?php

namespace App\Entities\Contracts;

use App\Entities\ValueObjects\Identifier;

/**
 * Interface for domain entities that require a unique identifier.
 */
interface IEntity
{
    /**
     * Returns the unique identifier of the entity.
     *
     * @return Identifier
     */
    public function getIdentifier(): Identifier;
}
