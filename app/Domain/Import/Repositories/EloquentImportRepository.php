<?php

namespace App\Domain\Import\Repositories;

use App\Domain\Import\Adapters\ImportAdapter;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Models\Import as ImportModel;
use DomainException;

/**
 * Class EloquentImportRepository
 *
 * Repository implementation for handling import persistence using Eloquent ORM.
 */
class EloquentImportRepository implements ImportRepositoryInterface
{
    /**
     * Create a new import record in the database.
     *
     * @param Import $import The import entity to persist.
     * @return Import The persisted import entity.
     */
    public function create(Import $import): Import
    {
        $model = ImportModel::create(ImportAdapter::toModel($import));
        return ImportAdapter::toEntity($model);
    }

    /**
     * Update an existing import record in the database.
     *
     * @param Import $import The import entity with updated data.
     * @return Import The updated import entity.
     */
    public function update(Import $import): Import
    {
        $model = ImportModel::findOrFail($import->getIdentifier()->value());
        $model->update(ImportAdapter::toModel($import));
        return ImportAdapter::toEntity($model);
    }

    /**
     * Find an import by its ID.
     *
     * @param int $id The ID of the import to find.
     * @return Import The found import entity.
     *
     * @throws DomainException If no import is found with the given ID.
     */
    public function findById(int $id): Import
    {
        $model = ImportModel::find($id);

        throw_if(!$model, new DomainException('Importação não encontrada'));
        
        return ImportAdapter::toEntity($model);
    }

    /**
     * List all imports with optional filters.
     *
     * @param array $filters Optional filters to apply to the query.
     * @return Import[] Array of import entities.
     */
    public function listImports(array $filters = []): array
    {
        $query = ImportModel::query();
        
        return $query->get()
            ->map(fn (ImportModel $model) => ImportAdapter::toEntity($model))
            ->toArray();
    }
}
