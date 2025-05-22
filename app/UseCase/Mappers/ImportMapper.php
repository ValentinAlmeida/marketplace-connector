<?php

namespace App\UseCase\Mappers;

use App\Entities\Enums\ImportStatus;
use App\Entities\Import;
use App\Models\Import as ImportModel;
use App\UseCase\Import\Dto\ImportRestoreDto;

/**
 * Class ImportMapper
 *
 * Provides static methods to map between the Import Eloquent model (ImportModel)
 * and the Import domain entity.
 */
class ImportMapper
{
    /**
     * Maps an ImportModel (Eloquent model) to an Import domain entity.
     *
     * @param ImportModel $model The Eloquent model instance.
     * @return Import The corresponding Import domain entity.
     */
    public static function toEntity(ImportModel $model): Import
    {
        return Import::restore(
            dto: new ImportRestoreDto(
                status: ImportStatus::from($model->{ImportModel::STATUS}),
                description: $model->{ImportModel::DESCRIPTION},
                processedItems: $model->{ImportModel::PROCESSED_ITEMS},
                totalItems: $model->{ImportModel::TOTAL_ITEMS},
                failedItems: $model->{ImportModel::FAILED_ITEMS} ?? 0,
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
     * Maps an Import domain entity to an array representation suitable for Eloquent model operations.
     *
     * This array typically contains attributes that can be used for creating or updating
     * an ImportModel instance.
     *
     * @param Import $entity The Import domain entity instance.
     * @return array<string, mixed> An array of attributes from the entity.
     */
    public static function toModel(Import $entity): array
    {
        $props = $entity->getProps();
        
        return [
            ImportModel::STATUS => $props->status->value,
            ImportModel::DESCRIPTION => $props->description,
            ImportModel::PROCESSED_ITEMS => $props->processedItems,
            ImportModel::TOTAL_ITEMS => $props->totalItems,
            ImportModel::FAILED_ITEMS => $props->failedItems,
            ImportModel::ERROR => $props->error,
            ImportModel::SCHEDULED_AT => $props->scheduledAt,
            ImportModel::STARTED_AT => $props->startedAt,
            ImportModel::COMPLETED_AT => $props->completedAt,
            ImportModel::METADATA => $props->metadata
        ];
    }
}