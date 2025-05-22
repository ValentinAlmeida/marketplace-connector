<?php

namespace App\Repositories;

use App\Entities\Import;
use App\Exceptions\ImportException;
use App\Models\Import as ImportModel;
use App\UseCase\Contracts\Repositories\IImportRepository;
use App\UseCase\Mappers\ImportMapper;

/**
 * Class EloquentImportRepository
 *
 * Repository implementation for handling import persistence using Eloquent ORM.
 */
class ImportRepository implements IImportRepository
{
    /**
     * Create a new import record in the database.
     *
     * @param Import $import The import entity to persist.
     * @return Import The persisted import entity.
     */
    public function create(Import $import): Import
    {
        $model = ImportModel::create(ImportMapper::toModel($import));
        return ImportMapper::toEntity($model);
    }

    /**
     * Update an existing import record in the database.
     *
     * @param Import $import The import entity with updated data.
     * @return void
     */
    public function update(Import $import): void
    {
        $model = ImportModel::findOrFail($import->getIdentifier()->value());
        $model->update(ImportMapper::toModel($import));
    }

    /**
     * Find an import by its ID.
     *
     * @param int $id The ID of the import to find.
     * @return Import The found import entity.
     *
     * @throws ImportException If no import is found with the given ID.
     */
    public function findById(int $id): Import
    {
        $model = ImportModel::find($id);

        throw_if(!$model, ImportException::notFoundById($id));
        
        return ImportMapper::toEntity($model);
    }
}
