<?php

namespace App\UseCase\Import\Dto;

use App\Entities\Enums\ImportStatus;
use Carbon\Carbon;

/**
 * Class ImportRestoreDto
 *
 * Data Transfer Object used for restoring an Import entity from its existing data.
 * All properties are read-only and are initialized via the constructor.
 * @final
 */
final class ImportRestoreDto
{
    /**
     * ImportRestoreDto constructor.
     *
     * @param ImportStatus $status The current status of the import.
     * @param string|null $description A description of the import's purpose or content.
     * @param int $processedItems The number of items that have been successfully processed.
     * @param int $totalItems The total number of items intended for processing in this import.
     * @param int $failedItems The number of items that failed during processing.
     * @param string|null $error A general error message if the import encountered a critical failure.
     * @param Carbon|null $scheduledAt The date and time when the import is scheduled to begin.
     * @param Carbon|null $startedAt The actual date and time when the import processing started.
     * @param Carbon|null $completedAt The date and time when the import processing finished (either completed or failed).
     * @param array|null $metadata A key-value store for any additional arbitrary data related to the import.
     * @param Carbon $createdAt The date and time when this import record was originally created.
     * @param Carbon $updatedAt The date and time when this import record was last modified.
     * @param Carbon|null $deletedAt The date and time when this import record was soft-deleted, if applicable.
     */
    public function __construct(
        public readonly ImportStatus $status,
        public readonly ?string $description,
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