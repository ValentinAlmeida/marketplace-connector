<?php

namespace App\UseCase\Import\Dto;

use App\Entities\Enums\ImportStatus;
use Carbon\Carbon;

/**
 * Data Transfer Object used to restore an existing Import entity
 * from persisted data (e.g., from the database).
 */
final class ImportRestoreDto
{
    /**
     * @param ImportStatus $status Current status of the import.
     * @param string|null $description Optional description of the import.
     * @param int $processedItems Number of items processed so far.
     * @param int $totalItems Total number of items to be processed.
     * @param string|null $error Error message if the import failed.
     * @param Carbon|null $scheduledAt Date and time when the import is scheduled to start.
     * @param Carbon|null $startedAt Date and time when the import actually started.
     * @param Carbon|null $completedAt Date and time when the import completed.
     * @param array<string, mixed>|null $metadata Additional metadata related to the import.
     * @param Carbon $createdAt Timestamp of when the import was created.
     * @param Carbon $updatedAt Timestamp of the last update to the import.
     * @param Carbon|null $deletedAt Timestamp if the import was soft-deleted.
     */
    public function __construct(
        public readonly ImportStatus $status,
        public readonly ?string $description,
        public readonly int $processedItems,
        public readonly int $totalItems,
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
