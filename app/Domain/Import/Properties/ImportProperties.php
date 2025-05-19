<?php

namespace App\Domain\Import\Properties;

use App\Domain\Import\Enums\ImportStatus;
use Carbon\Carbon;

final class ImportProperties
{
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