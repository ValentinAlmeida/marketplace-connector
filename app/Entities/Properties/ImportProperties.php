<?php

namespace App\Entities\Properties;

use App\Entities\Enums\ImportStatus;
use Carbon\Carbon;

/**
 * Value object that represents the properties of an import entity.
 */
final class ImportProperties
{
    /**
     * @param ImportStatus $status The current status of the import.
     * @param string|null $description A human-readable description of the import.
     * @param int $processedItems Number of items processed so far.
     * @param int $totalItems Total number of items expected to be processed.
     * @param string|null $error Error message, if the import has failed.
     * @param Carbon|null $scheduledAt Date and time the import is scheduled to run.
     * @param Carbon|null $startedAt Timestamp indicating when the import started.
     * @param Carbon|null $completedAt Timestamp indicating when the import was completed.
     * @param array|null $metadata Additional metadata related to the import.
     * @param Carbon $createdAt Timestamp indicating when the import was created.
     * @param Carbon $updatedAt Timestamp indicating when the import was last updated.
     * @param Carbon|null $deletedAt Timestamp indicating when the import was deleted (if applicable).
     */
    public function __construct(
        public readonly ImportStatus $status,
        public readonly ?string $description,
        public readonly int $processedItems,
        public readonly int $totalItems,
        public readonly ?string $error,
        public readonly ?Carbon $scheduledAt,
        public ?Carbon $startedAt,
        public ?Carbon $completedAt,
        public readonly ?array $metadata,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly ?Carbon $deletedAt
    ) {}
}
