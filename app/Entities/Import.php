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
use InvalidArgumentException;

/**
 * Represents an import process within the system.
 *
 * This entity manages the data and state transitions for an import.
 * It should be instantiated via its static factory methods `create` or `restore`.
 * @final
 */
final class Import extends BaseEntity
{
    private ImportProperties $props;
    private ImportState $state;

    /**
     * Private constructor to enforce instantiation via factory methods.
     *
     * @param ImportProperties $props The properties of the import.
     * @param Identifier|null $id The unique identifier of the entity, if it already exists.
     */
    private function __construct(ImportProperties $props, ?Identifier $id = null)
    {
        parent::__construct($id);
        $this->props = $props;
        $this->state = ImportStateFactory::make($props->status->value, $props->error);
    }

    /**
     * Creates a new Import entity.
     *
     * Initializes the import with a PENDING status and default values.
     *
     * @param ImportCreateDto $dto Data Transfer Object containing initial data for the import.
     * @return self A new instance of the Import entity.
     */
    public static function create(ImportCreateDto $dto): self
    {
        $now = Carbon::now();
        $props = new ImportProperties(
            status: ImportStatus::PENDING,
            description: $dto->description,
            processedItems: 0,
            totalItems: 0,
            failedItems: 0,
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
     * Restores an Import entity from its properties.
     *
     * Used to reconstitute an entity, typically from a persistent storage.
     *
     * @param ImportRestoreDto $dto Data Transfer Object containing all properties of an existing import.
     * @param int $id The unique identifier of the import.
     * @return self An instance of the Import entity.
     */
    public static function restore(ImportRestoreDto $dto, int $id): self
    {
        $props = new ImportProperties(
            status: $dto->status,
            description: $dto->description,
            processedItems: $dto->processedItems,
            totalItems: $dto->totalItems,
            failedItems: $dto->failedItems,
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
     * Changes the state of the import.
     *
     * Updates the internal state object and related properties like status and error.
     *
     * @param ImportState $newState The new state for the import.
     * @return void
     */
    public function changeState(ImportState $newState): void
    {
        $this->state = $newState;
        $currentError = $this->props->error;
        if ($newState instanceof FailedState && $newState->getError()) {
            $currentError = $newState->getError();
        }

        $this->props = new ImportProperties(
            status: ImportStatus::from($newState->getStatus()),
            description: $this->props->description,
            processedItems: $this->props->processedItems,
            totalItems: $this->props->totalItems,
            failedItems: $this->props->failedItems,
            error: $currentError,
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
     * Sets the started_at timestamp for the import.
     *
     * @param Carbon $startedAt The timestamp when the import process started.
     * @return void
     */
    public function setStartedAt(Carbon $startedAt): void
    {
        $this->props = new ImportProperties(
            $this->props->status, $this->props->description, $this->props->processedItems,
            $this->props->totalItems, $this->props->failedItems, $this->props->error,
            $this->props->scheduledAt, $startedAt, $this->props->completedAt,
            $this->props->metadata, $this->props->createdAt, Carbon::now(), $this->props->deletedAt
        );
    }

    /**
     * Sets the completed_at timestamp for the import.
     *
     * @param Carbon $completedAt The timestamp when the import process completed.
     * @return void
     */
    public function setCompletedAt(Carbon $completedAt): void
    {
        $this->props = new ImportProperties(
            $this->props->status, $this->props->description, $this->props->processedItems,
            $this->props->totalItems, $this->props->failedItems, $this->props->error,
            $this->props->scheduledAt, $this->props->startedAt, $completedAt,
            $this->props->metadata, $this->props->createdAt, Carbon::now(), $this->props->deletedAt
        );
    }

    /**
     * Updates the progress of the import.
     *
     * @param int $processedItems The number of items processed so far.
     * @param int $totalItems The total number of items to be processed.
     * @return void
     * @throws InvalidArgumentException If processed or total items are negative, or processed exceeds total.
     */
    public function updateProgress(int $processedItems, int $totalItems): void
    {
        if ($totalItems < 0 || $processedItems < 0) {
            throw new InvalidArgumentException('Processed or total items cannot be negative.');
        }
        if ($processedItems > $totalItems && $totalItems > 0) { 
             throw new InvalidArgumentException('Processed items cannot exceed total items.');
        }

        $this->props = new ImportProperties(
            status: $this->props->status,
            description: $this->props->description,
            processedItems: $processedItems,
            totalItems: $totalItems,
            failedItems: ($processedItems == 0 && $totalItems >=0) ? 0 : $this->props->failedItems,
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

    /**
     * Increments the count of processed items by one.
     *
     * @return void
     */
    public function incrementProcessedItems(): void
    {
        $this->props = new ImportProperties(
            $this->props->status, $this->props->description, $this->props->processedItems + 1,
            $this->props->totalItems, $this->props->failedItems, $this->props->error,
            $this->props->scheduledAt, $this->props->startedAt, $this->props->completedAt,
            $this->props->metadata, $this->props->createdAt, Carbon::now(), $this->props->deletedAt
        );
    }

    /**
     * Increments the count of failed items by one.
     *
     * @return void
     */
    public function incrementFailedItems(): void
    {
        $this->props = new ImportProperties(
            $this->props->status, $this->props->description, $this->props->processedItems,
            $this->props->totalItems, $this->props->failedItems + 1, $this->props->error,
            $this->props->scheduledAt, $this->props->startedAt, $this->props->completedAt,
            $this->props->metadata, $this->props->createdAt, Carbon::now(), $this->props->deletedAt
        );
    }
    
    /**
     * Adds a detail to the error metadata of the import.
     *
     * Error details are stored in an array under the 'error_details' key in metadata.
     *
     * @param string $key The key for the error detail (e.g., 'item_id', 'validation_error').
     * @param mixed $value The value of the error detail.
     * @return void
     */
    public function addErrorDetail(string $key, mixed $value): void
    {
        $metadata = $this->props->metadata ?? [];
        if (!isset($metadata['error_details'])) {
            $metadata['error_details'] = [];
        }
        $metadata['error_details'][$key][] = $value;

        $this->props = new ImportProperties(
            $this->props->status, $this->props->description, $this->props->processedItems,
            $this->props->totalItems, $this->props->failedItems, $this->props->error,
            $this->props->scheduledAt, $this->props->startedAt, $this->props->completedAt,
            $metadata, $this->props->createdAt, Carbon::now(), $this->props->deletedAt
        );
    }

    /**
     * Transitions the import to a processing state. Delegates to the current state object.
     * @return void
     */
    public function startProcessing(): void { $this->state->startProcessing($this); }

    /**
     * Completes the import process. Delegates to the current state object.
     * @return void
     */
    public function complete(): void { $this->state->complete($this); }

    /**
     * Fails the import process with a given error message. Delegates to the current state object.
     * @param string $error The error message describing the failure.
     * @return void
     */
    public function fail(string $error): void { $this->state->fail($this, $error); }

    /**
     * Cancels the import process. Delegates to the current state object.
     * @return void
     */
    public function cancel(): void { $this->state->cancel($this); }

    /**
     * Gets the properties of the import.
     * @return ImportProperties The current properties of the import.
     */
    public function getProps(): ImportProperties { return $this->props; }

    /**
     * Gets the current state object of the import.
     * @return ImportState The current state object.
     */
    public function getState(): ImportState { return $this->state; }

    /**
     * Gets a human-readable description of the current import status.
     * @return string The human-readable status.
     */
    public function getStatusDescription(): string { return $this->state->getHumanStatus(); }
}