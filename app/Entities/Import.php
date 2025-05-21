<?php

namespace App\Entities;

use App\Entities\Enums\ImportStatus;
use App\Entities\Properties\ImportProperties;
use App\Entities\States\Import\FailedState;
use App\Entities\States\Import\ImportState;
use App\Entities\ValueObjects\Identifier;
use App\UseCase\Import\Dto\ImportCreateDto;
use App\UseCase\Import\Dto\ImportRestoreDto;
use App\UseCase\Import\Factories\ImportStateFactory;
use Carbon\Carbon;

/**
 * Class Import
 *
 * Represents an import process with state management and domain behavior.
 */
final class Import extends BaseEntity
{
    private ImportProperties $props;
    private ImportState $state;

    /**
     * Import constructor.
     *
     * @param ImportProperties $props
     * @param Identifier|null $id
     */
    private function __construct(ImportProperties $props, ?Identifier $id = null)
    {
        parent::__construct($id);
        $this->props = $props;
        $this->state = ImportStateFactory::make($props->status->value, $props->error);
    }

    /**
     * Factory method to create a new Import from a DTO.
     *
     * @param ImportCreateDto $dto
     * @return self
     */
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

    /**
     * Factory method to restore an Import from persisted data.
     *
     * @param ImportRestoreDto $dto
     * @param int $id
     * @return self
     */
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

    /**
     * Changes the current state of the import.
     *
     * @param ImportState $newState
     * @return void
     */
    public function changeState(ImportState $newState): void
    {
        $this->state = $newState;
        $this->props = new ImportProperties(
            status: ImportStatus::from($newState->getStatus()),
            description: $this->props->description,
            processedItems: $this->props->processedItems,
            totalItems: $this->props->totalItems,
            error: $newState instanceof FailedState
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

    /**
     * Sets the import's start time.
     *
     * @param Carbon $startedAt
     * @return void
     */
    public function setStartedAt(Carbon $startedAt): void
    {
        $this->props->startedAt = $startedAt;
    }

    /**
     * Sets the import's completion time.
     *
     * @param Carbon $completedAt
     * @return void
     */
    public function setCompletedAt(Carbon $completedAt): void
    {
        $this->props->completedAt = $completedAt;
    }

    /**
     * Executes logic for starting the import.
     *
     * @return void
     */
    public function startProcessing(): void
    {
        $this->state->startProcessing($this);
    }

    /**
     * Executes logic for completing the import.
     *
     * @return void
     */
    public function complete(): void
    {
        $this->state->complete($this);
    }

    /**
     * Executes logic for failing the import with an error.
     *
     * @param string $error
     * @return void
     */
    public function fail(string $error): void
    {
        $this->state->fail($this, $error);
    }

    /**
     * Executes logic for canceling the import.
     *
     * @return void
     */
    public function cancel(): void
    {
        $this->state->cancel($this);
    }

    /**
     * Returns the import's properties.
     *
     * @return ImportProperties
     */
    public function getProps(): ImportProperties
    {
        return $this->props;
    }

    /**
     * Returns the current state object of the import.
     *
     * @return ImportState
     */
    public function getState(): ImportState
    {
        return $this->state;
    }

    /**
     * Returns a human-readable description of the current status.
     *
     * @return string
     */
    public function getStatusDescription(): string
    {
        return $this->state->getHumanStatus();
    }

    /**
     * Updates the import's progress values.
     *
     * @param int $processedItems
     * @param int $totalItems
     * @return void
     */
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
