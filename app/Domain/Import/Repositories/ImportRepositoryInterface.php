<?php

namespace App\Domain\Import\Repositories;

use App\Domain\Import\Entity\Import;

/**
 * Interface ImportRepositoryInterface
 *
 * Defines the contract for import data persistence.
 */
interface ImportRepositoryInterface
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
     * @return Import The updated import entity
     */
    public function update(Import $import): Import;

    /**
     * Find an import by its ID.
     *
     * @param int $id The ID of the import
     * @return Import The found import entity
     */
    public function findById(int $id): Import;

    /**
     * List imports filtered by given criteria.
     *
     * @param array $filters Optional filters for querying imports
     * @return Import[] An array of import entities
     */
    public function listImports(array $filters = []): array;
}
