<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Domain\Shared\UnitOfWork\UnitOfWorkInterface;

class CreateImportUseCase
{
    public function __construct(
        private ImportRepositoryInterface $repository,
        private UnitOfWorkInterface $unitOfWork,
        private ScheduleImportJobUseCase $scheduleJobUseCase
    ) {}

    public function execute(ImportCreateDto $dto): Import
    {
        return $this->unitOfWork->run(function () use ($dto) {
            $import = Import::create($dto);
            $savedImport = $this->repository->create($import);
            
            $this->scheduleJobUseCase->execute($savedImport);
            
            return $savedImport;
        });
    }
}