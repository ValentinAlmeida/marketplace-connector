<?php

namespace App\Domain\Shared\ValueObjects;

/**
 * Value Object representing a unique reference.
 * Encapsulates a string value and provides access to it.
 */
final class Reference
{
    /**
     * Constructs a new Reference instance.
     *
     * @param string $value The reference value.
     */
    public function __construct(private readonly string $value) {}

    /**
     * Returns the reference value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }
}
