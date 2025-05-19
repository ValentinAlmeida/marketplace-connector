<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Jobs\ProcessImportJob;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Domain\Shared\UnitOfWork\UnitOfWorkInterface;
use App\Domain\Shared\ValueObjects\Identifier;
use Illuminate\Support\Facades\App;

class ImportService implements ImportServiceInterface
{
    public function __construct(
        private ImportRepositoryInterface $repository,
    ) {}

    public function createImport(ImportCreateDto $dto): Import
    {
        return App::make(UnitOfWorkInterface::class)->run(function() use ($dto) {
            $import = Import::create($dto);
            $savedImport = $this->repository->create($import);
            // $this->dispatchImportJob($savedImport);
            return $savedImport;
        });
    }

    public function updateImport(Import $import): Import
    {
        return $this->repository->update($import);
    }

    public function findImport(int $id): Import
    {
        return $this->repository->findById($id);
    }

    // private function dispatchImportJob(Import $import): void
    // {
    //     ProcessImportJob::dispatch($import->getIdentifier())
    //         ->onQueue('imports')
    //         ->delay($import->getProps()->scheduledAt);
    // }
}