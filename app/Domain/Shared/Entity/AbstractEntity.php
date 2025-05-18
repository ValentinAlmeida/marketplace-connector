<?php

namespace App\Domain\Shared\Entity;

use App\Domain\Shared\ValueObjects\Identifier;

abstract class AbstractEntity
{
    public function __construct(
        protected readonly Identifier $id
    ) {}

    public function getIdentifier(): Identifier
    {
        return $this->id;
    }
}