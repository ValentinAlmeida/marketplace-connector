<?php

namespace App\UseCase\Import;

use App\Entities\Import;
use App\UnitOfWork\Contract\IUnitOfWork;
use App\UseCase\Contracts\Import\ICreate;
use App\UseCase\Contracts\Repositories\IImportRepository;
use App\UseCase\Import\Dto\ImportCreateDto;

/**
 * Class CreateImportUseCase
 *
 * Handles the creation of a new import, including persistence and job scheduling.
 */
class Create implements ICreate
{
    /**
     * @param ImportRepositoryInterface $repository Repository for saving the import
     * @param UnitOfWorkInterface $unitOfWork Unit of work to ensure atomic operations
     * @param ScheduleImportJobUseCase $scheduleJobUseCase Use case for scheduling the import job
     */
    public function __construct(
        private IImportRepository $repository,
        private IUnitOfWork $unitOfWork,
        private Schedule $scheduleJobUseCase
    ) {}

    /**
     * Executes the use case for creating a new import.
     *
     * @param ImportCreateDto $dto Data transfer object containing import creation data
     * @return void
     */
    public function execute(ImportCreateDto $dto): void
    {
        $this->unitOfWork->run(function () use ($dto) {
            $import = Import::create($dto);
            $savedImport = $this->repository->create($import);
            
            $this->scheduleJobUseCase->execute($savedImport);
        });
    }
}
