<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;

class ImportService implements ImportServiceInterface
{
    public function __construct(
        private ImportRepositoryInterface $repository
    ) {}

    public function createImport(ImportCreateDto $dto): Import
    {
        $import = Import::create($dto);
        return $this->repository->create($import);
    }

    public function getImportStatus(int $id): Import
    {
        return $this->repository->findById($id);
    }
}