<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Domain\Import\UseCases\CreateImportUseCase;
use App\Domain\Import\UseCases\UpdateImportUseCase;

/**
 * Class ImportService
 *
 * Service layer responsible for handling import-related operations.
 */
class ImportService implements ImportServiceInterface
{
    /**
     * ImportService constructor.
     *
     * @param ImportRepositoryInterface $repository
     * @param CreateImportUseCase $createUseCase
     * @param UpdateImportUseCase $updateUseCase
     */
    public function __construct(
        private ImportRepositoryInterface $repository,
        private CreateImportUseCase $createUseCase,
        private UpdateImportUseCase $updateUseCase
    ) {}

    /**
     * Create a new import record.
     *
     * @param ImportCreateDto $dto Data transfer object containing import data
     * @return Import The created import entity
     */
    public function createImport(ImportCreateDto $dto): Import
    {
        return $this->createUseCase->execute($dto);
    }

    /**
     * Update an existing import record.
     *
     * @param Import $import The import entity to update
     * @return Import The updated import entity
     */
    public function updateImport(Import $import): Import
    {
        return $this->updateUseCase->execute($import);
    }

    /**
     * Find an import by its ID.
     *
     * @param int $id The ID of the import
     * @return Import The found import entity
     */
    public function findImport(int $id): Import
    {
        return $this->repository->findById($id);
    }
}
