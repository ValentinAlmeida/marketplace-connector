<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Domain\Import\UseCases\CreateImportUseCase;
use App\Domain\Import\UseCases\UpdateImportUseCase;

class ImportService implements ImportServiceInterface
{
    public function __construct(
        private ImportRepositoryInterface $repository,
        private CreateImportUseCase $createUseCase,
        private UpdateImportUseCase $updateUseCase
    ) {}

    public function createImport(ImportCreateDto $dto): Import
    {
        return $this->createUseCase->execute($dto);
    }

    public function updateImport(Import $import): Import
    {
        return $this->updateUseCase->execute($import);
    }

    public function findImport(int $id): Import
    {
        return $this->repository->findById($id);
    }
}