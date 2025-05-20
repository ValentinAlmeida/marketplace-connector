<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;

/**
 * Interface ImportServiceInterface
 *
 * Defines the contract for import service operations.
 */
interface ImportServiceInterface
{
    /**
     * Create a new import.
     *
     * @param ImportCreateDto $dto Data Transfer Object containing import creation data
     * @return Import The created import entity
     */
    public function createImport(ImportCreateDto $dto): Import;

    /**
     * Update an existing import.
     *
     * @param Import $dto The import entity to update
     * @return Import The updated import entity
     */
    public function updateImport(Import $dto): Import;

    /**
     * Find an import by its identifier.
     *
     * @param int $importId The unique identifier of the import
     * @return Import The import entity
     */
    public function findImport(int $importId): Import;
}
