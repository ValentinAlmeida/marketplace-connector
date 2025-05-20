<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Entity\Import;
use App\Domain\Import\Jobs\ImportOffersJob;

class ScheduleImportJobUseCase
{
    public function execute(Import $import): void
    {
        ImportOffersJob::dispatch($import->getIdentifier()->value())
            ->delay($import->getProps()->scheduledAt);
    }
}