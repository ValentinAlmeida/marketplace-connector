<?php

namespace App\Domain\Shared\ValueObjects;

final class Identifier
{
    private function __construct(
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

    public static function create(string $value): static
    {
        return new static($value);
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