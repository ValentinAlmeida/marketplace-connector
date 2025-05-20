<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Domain\Shared\UnitOfWork\UnitOfWorkInterface;

/**
 * Class CreateImportUseCase
 *
 * Handles the creation of a new import, including persistence and job scheduling.
 */
class CreateImportUseCase
{
    /**
     * @param ImportRepositoryInterface $repository Repository for saving the import
     * @param UnitOfWorkInterface $unitOfWork Unit of work to ensure atomic operations
     * @param ScheduleImportJobUseCase $scheduleJobUseCase Use case for scheduling the import job
     */
    public function __construct(
        private ImportRepositoryInterface $repository,
        private UnitOfWorkInterface $unitOfWork,
        private ScheduleImportJobUseCase $scheduleJobUseCase
    ) {}

    /**
     * Executes the use case for creating a new import.
     *
     * @param ImportCreateDto $dto Data transfer object containing import creation data
     * @return Import The created and persisted import entity
     */
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
