<?php

namespace App\Entities\ValueObjects;

/**
 * Value Object representing a unique identifier.
 * Provides validation, comparison, and basic access to the identifier value.
 */
final class Identifier
{
    /**
     * Constructs a new Identifier instance.
     *
     * @param mixed $value The underlying identifier value.
     *
     * @throws \InvalidArgumentException If the value is empty.
     */
    private function __construct(
        private readonly mixed $value
    ) {
        $this->validate();
    }

    /**
     * Validates the identifier value.
     *
     * @throws \InvalidArgumentException If the value is empty.
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new \InvalidArgumentException("Identifier cannot be empty");
        }
    }

    /**
     * Returns the identifier value.
     *
     * @return mixed
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Factory method to create a new Identifier instance.
     *
     * @param string $value
     * @return static
     */
    public static function create(string $value): static
    {
        return new static($value);
    }

    /**
     * Compares this identifier to another for equality.
     *
     * @param self $other
     * @return bool
     */
    public function equals(self $other): bool
    {
        return $this->value() === $other->value();
    }

    /**
     * Returns the string representation of the identifier.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }
}
