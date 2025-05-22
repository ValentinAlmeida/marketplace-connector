<?php

namespace App\Entities\Enums;

/**
 * Enum ImportStatus
 *
 * Defines the possible statuses for an import process.
 * Each status has a string value, typically used as an identifier or translation key.
 */
enum ImportStatus: string
{
    case PENDING = 'import.status.pending';
    case PROCESSING = 'import.status.processing';
    case COMPLETED = 'import.status.completed';
    case FAILED = 'import.status.failed';
    case CANCELLED = 'import.status.cancelled';

    /**
     * Provides metadata associated with the status, primarily a human-readable description.
     *
     * @return array An array containing metadata, with a 'description' key.
     */
    public function withMeta(): array
    {
        return match ($this) {
            self::PENDING => ['description' => 'Pending'],
            self::PROCESSING => ['description' => 'Processing'],
            self::COMPLETED => ['description' => 'Completed'],
            self::FAILED => ['description' => 'Failed'],
            self::CANCELLED => ['description' => 'Cancelled'],
        };
    }

    /**
     * Checks if the current status is FAILED.
     *
     * @return bool True if the status is FAILED, false otherwise.
     */
    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    /**
     * Checks if the current status represents a final state of the import process.
     * Final states are COMPLETED, FAILED, or CANCELLED.
     *
     * @return bool True if the status is a final state, false otherwise.
     */
    public function isFinal(): bool
    {
        return match ($this) {
            self::COMPLETED, self::FAILED, self::CANCELLED => true,
            default => false,
        };
    }
}