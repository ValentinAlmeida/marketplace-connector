<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;

interface ImportServiceInterface
{
    public function createImport(ImportCreateDto $dto): Import;
    public function getImportStatus(int $importId): Import;
}