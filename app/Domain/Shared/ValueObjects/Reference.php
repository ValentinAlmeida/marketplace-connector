<?php

namespace App\Domain\Shared\ValueObjects;

final class Reference
{
    public function __construct(private readonly string $value) {}

    public function value(): string
    {
        return $this->value;
    }
}