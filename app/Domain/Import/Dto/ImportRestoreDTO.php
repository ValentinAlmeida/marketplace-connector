<?php

namespace App\Domain\Import\Dto;

use App\Domain\Import\Enums\ImportStatus;
use Carbon\Carbon;

final class ImportRestoreDto
{
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