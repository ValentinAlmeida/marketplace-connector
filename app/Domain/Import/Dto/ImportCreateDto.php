<?php

namespace App\Domain\Import\Dto;

use Carbon\Carbon;

final class ImportCreateDto
{
    public function __construct(
        public readonly ?string $description,
        public readonly ?Carbon $scheduledAt
    ) {}
}