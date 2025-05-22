<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;
use App\UseCase\Import\Dto\ImportCreateDto;

/**
 * Interface ImportServiceInterface
 *
 * Defines the contract for import service operations.
 */
interface IImportProcessor
{
    /**
     * Create a new import.
     *
     * @param ImportCreateDto $dto Data Transfer Object containing import creation data
     * @return void
     */
    public function createImport(ImportCreateDto $dto): void;

    /**
     * Update an existing import.
     *
     * @param Import $dto The import entity to update
     * @return void
     */
    public function updateImport(Import $dto): void;

    /**
     * Find an import by its identifier.
     *
     * @param int $importId The unique identifier of the import
     * @return Import The import entity
     */
    public function findImport(int $importId): Import;
}
