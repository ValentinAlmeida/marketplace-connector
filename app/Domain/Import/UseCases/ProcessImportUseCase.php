<?php

namespace App\Domain\Import\UseCases;

use App\Domain\Import\Events\ImportStarted;

class ProcessImportUseCase
{
    public function execute(int $importId): void
    {
        event(new ImportStarted($importId));
    }
}
