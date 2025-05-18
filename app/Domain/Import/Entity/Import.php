<?php

namespace App\Domain\Import\Entity;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Dto\ImportRestoreDto;
use App\Domain\Import\Enums\ImportStatus;
use App\Domain\Import\Factories\ImportStateFactory;
use App\Domain\Import\States\ImportState;
use App\Domain\Import\Properties\ImportProperties;
use App\Domain\Shared\Entity\AbstractEntity;
use App\Domain\Shared\ValueObjects\Identifier;
use Carbon\Carbon;

final class Import extends AbstractEntity
{
    private ImportProperties $props;
    private ImportState $state;

    private function __construct(ImportProperties $props, ?Identifier $id = null)
    {
        parent::__construct($id);
        $this->props = $props;
        $this->state = ImportStateFactory::make($props->status->value, $props->error);
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

        return new self($props);
    }

    public static function restore(ImportRestoreDto $dto, int $id): self
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

        return new self($props, Identifier::create($id));
    }

    public function changeState(ImportState $newState): void
    {
        $this->state = $newState;
        $this->props = new ImportProperties(
            status: ImportStatus::from($newState->getStatus()),
            description: $this->props->description,
            processedItems: $this->props->processedItems,
            totalItems: $this->props->totalItems,
            error: $newState instanceof \App\Domain\Import\States\FailedState 
                ? $newState->getError() 
                : null,
            scheduledAt: $this->props->scheduledAt,
            startedAt: $this->props->startedAt,
            completedAt: $this->props->completedAt,
            metadata: $this->props->metadata,
            createdAt: $this->props->createdAt,
            updatedAt: Carbon::now(),
            deletedAt: $this->props->deletedAt
        );
    }

    public function startProcessing(): void
    {
        $this->state->startProcessing($this);
    }

    public function complete(): void
    {
        $this->state->complete($this);
    }

    public function fail(string $error): void
    {
        $this->state->fail($this, $error);
    }

    public function cancel(): void
    {
        $this->state->cancel($this);
    }

    public function getProps(): ImportProperties
    {
        return $this->props;
    }

    public function getState(): ImportState
    {
        return $this->state;
    }

    public function getStatusDescription(): string
    {
        return $this->state->getHumanStatus();
    }

    public function updateProgress(int $processedItems, int $totalItems): void
    {
        $this->props = new ImportProperties(
            status: $this->props->status,
            description: $this->props->description,
            processedItems: $processedItems,
            totalItems: $totalItems,
            error: $this->props->error,
            scheduledAt: $this->props->scheduledAt,
            startedAt: $this->props->startedAt,
            completedAt: $this->props->completedAt,
            metadata: $this->props->metadata,
            createdAt: $this->props->createdAt,
            updatedAt: Carbon::now(),
            deletedAt: $this->props->deletedAt
        );
    }
}