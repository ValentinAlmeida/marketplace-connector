<?php

namespace App\Entities\Properties;

use App\Entities\Enums\ImportStatus;
use Carbon\Carbon;

/**
 * Class ImportProperties
 *
 * A data object representing the immutable properties of an Import entity.
 * This class uses constructor property promotion and all properties are read-only.
 * @final
 */
final class ImportProperties
{
    /**
     * Constructs a new ImportProperties instance.
     *
     * @param ImportStatus $status The current status of the import.
     * @param string $description A description of the import's purpose or content.
     * @param int $processedItems The number of items that have been successfully processed.
     * @param int $totalItems The total number of items intended for processing in this import.
     * @param int $failedItems The number of items that failed during processing.
     * @param string|null $error A general error message if the import encountered a critical failure.
     * @param Carbon|null $scheduledAt The date and time when the import is scheduled to begin.
     * @param Carbon|null $startedAt The actual date and time when the import processing started.
     * @param Carbon|null $completedAt The date and time when the import processing finished (either completed or failed).
     * @param array|null $metadata A key-value store for any additional arbitrary data related to the import (e.g., error details, configuration settings).
     * @param Carbon $createdAt The date and time when this import record was created.
     * @param Carbon $updatedAt The date and time when this import record was last modified.
     * @param Carbon|null $deletedAt The date and time when this import record was soft-deleted, if applicable.
     */
    public function __construct(
        public readonly ImportStatus $status,
        public readonly string $description,
        public readonly int $processedItems,
        public readonly int $totalItems,
        public readonly int $failedItems,
        public readonly ?string $error,
        public readonly ?Carbon $scheduledAt,
        public readonly ?Carbon $startedAt,
        public readonly ?Carbon $completedAt,
        public readonly ?array $metadata,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly ?Carbon $deletedAt
    ) {}
}