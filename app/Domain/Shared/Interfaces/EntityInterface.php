<?php

namespace App\Domain\Shared\Entity\Interfaces;

use App\Domain\Shared\ValueObjects\Identifier;

interface EntityInterface
{
    public function getIdentifier(): Identifier;
}