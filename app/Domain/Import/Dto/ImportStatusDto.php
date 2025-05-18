<?php

namespace App\Domain\Import\Dto;

use App\Domain\Import\Enums\ImportStatus;
use App\Domain\Import\ValueObjects\ImportProgress;

final class ImportStatusDto
{
    public function __construct(
        public readonly ImportStatus $status,
        public readonly ImportProgress $progress,
        public readonly ?string $error = null
    ) {}

    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'status_description' => $this->status->withMeta()['description'],
            'progress' => $this->progress->toArray(),
            'error' => $this->error
        ];
    }
}