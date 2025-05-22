<?php

namespace App\UseCase\Contracts\Repositories;

use App\Entities\Import;

/**
 * Interface ImportRepositoryInterface
 *
 * Defines the contract for import data persistence.
 */
interface IImportRepository
{
    /**
     * Persist a new import record.
     *
     * @param Import $import The import entity to be created
     * @return Import The newly created import entity
     */
    public function create(Import $import): Import;

    /**
     * Update an existing import record.
     *
     * @param Import $import The import entity to be updated
     * @return void
     */
    public function update(Import $import): void;

    /**
     * Find an import by its ID.
     *
     * @param int $id The ID of the import
     * @return Import The found import entity
     */
    public function findById(int $id): Import;
}
