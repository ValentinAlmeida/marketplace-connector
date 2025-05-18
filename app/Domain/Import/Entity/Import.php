<?php

namespace App\Domain\Import\Entity;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Dto\ImportRestoreDto;
use App\Domain\Import\Enums\ImportStatus;
use App\Domain\Import\Properties\ImportProperties;
use App\Domain\Shared\Entity\AbstractEntity;
use App\Domain\Shared\ValueObjects\Identifier;
use Carbon\Carbon;

final class Import extends AbstractEntity
{
    private ImportProperties $props;

    private function __construct(Identifier $id, ImportProperties $props)
    {
        parent::__construct($id);
        $this->props = $props;
    }

    public static function create(ImportCreateDto $dto): self
    {
        $now = Carbon::now();

        $props = new ImportProperties(
            status: ImportStatus::PENDING,
            description: $dto->description,
            processedItems: 0,
            totalItems: 0,
            error: null,
            scheduledAt: $dto->scheduledAt,
            startedAt: null,
            completedAt: null,
            metadata: null,
            createdAt: $now,
            updatedAt: $now,
            deletedAt: null
        );

        return new self(new Identifier(null), $props);
    }

    public static function restore(ImportRestoreDto $dto, Identifier $id): self
    {
        $props = new ImportProperties(
            status: $dto->status,
            description: $dto->description,
            processedItems: $dto->processedItems,
            totalItems: $dto->totalItems,
            error: $dto->error,
            scheduledAt: $dto->scheduledAt,
            startedAt: $dto->startedAt,
            completedAt: $dto->completedAt,
            metadata: $dto->metadata,
            createdAt: $dto->createdAt,
            updatedAt: $dto->updatedAt,
            deletedAt: $dto->deletedAt
        );

        return new self($id, $props);
    }

    public function getProps(): ImportProperties
    {
        return $this->props;
    }

    public function getStatusDescription(): string
    {
        return $this->props->status->withMeta()['description'];
    }
}