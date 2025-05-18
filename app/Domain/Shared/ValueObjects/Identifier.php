<?php

namespace App\Domain\Shared\ValueObjects;

final class Identifier
{
    public function __construct(
        private readonly mixed $value
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new \InvalidArgumentException("Identifier cannot be empty");
        }
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value() === $other->value();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}