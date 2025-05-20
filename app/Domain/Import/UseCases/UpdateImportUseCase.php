<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;

/**
 * Use case responsible for updating an import entity.
 *
 * This class handles the persistence of changes made to an import object.
 */
class UpdateImportUseCase
{
    /**
     * @param ImportRepositoryInterface $repository The repository responsible for import persistence.
     */
    public function __construct(
        private ImportRepositoryInterface $repository
    ) {}

    /**
     * Execute the update process for the given import entity.
     *
     * @param Import $import The import entity to be updated.
     * @return Import The updated import entity.
     */
    public function execute(Import $import): Import
    {
        return $this->repository->update($import);
    }
}
