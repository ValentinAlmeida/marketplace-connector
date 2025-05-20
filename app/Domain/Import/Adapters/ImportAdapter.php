<?php

namespace App\Domain\Import\Adapters;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Enums\ImportStatus;
use App\Models\Import as ImportModel;

/**
 * Class ImportAdapter
 *
 * Responsible for converting between Import entities and Eloquent models.
 */
class ImportAdapter
{
    /**
     * Converts an Eloquent Import model to a domain Import entity.
     *
     * @param ImportModel $model The Eloquent model representing the import.
     * @return Import The corresponding domain Import entity.
     */
    public static function toEntity(ImportModel $model): Import
    {
        return Import::restore(
            dto: new \App\Domain\Import\Dto\ImportRestoreDto(
                status: ImportStatus::from($model->{ImportModel::STATUS}),
                description: $model->{ImportModel::DESCRIPTION},
                processedItems: $model->{ImportModel::PROCESSED_ITEMS},
                totalItems: $model->{ImportModel::TOTAL_ITEMS},
                error: $model->{ImportModel::ERROR},
                scheduledAt: $model->{ImportModel::SCHEDULED_AT},
                startedAt: $model->{ImportModel::STARTED_AT},
                completedAt: $model->{ImportModel::COMPLETED_AT},
                metadata: $model->{ImportModel::METADATA},
                createdAt: $model->{ImportModel::CREATED_AT},
                updatedAt: $model->{ImportModel::UPDATED_AT},
                deletedAt: $model->{ImportModel::DELETED_AT}
            ),
            id: $model->getKey()
        );
    }

    /**
     * Converts a domain Import entity to a format suitable for Eloquent persistence.
     *
     * @param Import $entity The domain Import entity.
     * @return array Associative array of attributes to be used by the Eloquent model.
     */
    public static function toModel(Import $entity): array
    {
        $props = $entity->getProps();
        
        return [
            ImportModel::STATUS => $props->status->value,
            ImportModel::DESCRIPTION => $props->description,
            ImportModel::PROCESSED_ITEMS => $props->processedItems,
            ImportModel::TOTAL_ITEMS => $props->totalItems,
            ImportModel::ERROR => $props->error,
            ImportModel::SCHEDULED_AT => $props->scheduledAt,
            ImportModel::STARTED_AT => $props->startedAt,
            ImportModel::COMPLETED_AT => $props->completedAt,
            ImportModel::METADATA => $props->metadata
        ];
    }
}
