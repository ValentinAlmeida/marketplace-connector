<?php

namespace App\UseCase\Import\Dto;

use Carbon\Carbon;

/**
 * Data Transfer Object for creating a new Import.
 */
final class ImportCreateDto
{
    /**
     * @param string|null $description Optional description for the import.
     * @param Carbon|null $scheduledAt Optional scheduled date and time for the import.
     */
    public function __construct(
        public readonly ?string $description,
        public readonly ?Carbon $scheduledAt
    ) {}
}
