<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;

class UpdateImportUseCase
{
    public function __construct(
        private ImportRepositoryInterface $repository
    ) {}

    public function execute(Import $import): Import
    {
        return $this->repository->update($import);
    }
}