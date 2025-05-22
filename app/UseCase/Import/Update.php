<?php

namespace App\UseCase\Import;

use App\Entities\Import;
use App\UseCase\Contracts\Import\IUpdate;
use App\UseCase\Contracts\Repositories\IImportRepository;

/**
 * Use case responsible for updating an import entity.
 *
 * This class handles the persistence of changes made to an import object.
 */
class Update implements IUpdate
{
    /**
     * @param ImportRepositoryInterface $repository The repository responsible for import persistence.
     */
    public function __construct(
        private IImportRepository $repository
    ) {}

    /**
     * Execute the update process for the given import entity.
     *
     * @param Import $import The import entity to be updated.
     * @return void
     */
    public function execute(Import $import): void
    {
        $this->repository->update($import);
    }
}
