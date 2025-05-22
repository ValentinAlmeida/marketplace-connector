<?php

namespace App\UseCase\Import;

use App\Entities\Import;
use App\UseCase\Contracts\Import\ICreate;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Contracts\Import\IUpdate;
use App\UseCase\Contracts\Repositories\IImportRepository;
use App\UseCase\Import\Dto\ImportCreateDto;

/**
 * Class ImportService
 *
 * Service layer responsible for handling import-related operations.
 */
class ImportProcessor implements IImportProcessor
{
    /**
     * ImportService constructor.
     *
     * @param ImportRepositoryInterface $repository
     * @param CreateImportUseCase $createUseCase
     * @param UpdateImportUseCase $updateUseCase
     */
    public function __construct(
        private IImportRepository $repository,
        private ICreate $createUseCase,
        private IUpdate $updateUseCase
    ) {}

    /**
     * Create a new import record.
     *
     * @param ImportCreateDto $dto Data transfer object containing import data
     * @return void
     */
    public function createImport(ImportCreateDto $dto): void
    {
        $this->createUseCase->execute($dto);
    }

    /**
     * Update an existing import record.
     *
     * @param Import $import The import entity to update
     * @return void
     */
    public function updateImport(Import $import): void
    {
        $this->updateUseCase->execute($import);
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
