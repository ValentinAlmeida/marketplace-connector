<?php

namespace App\Domain\Import\Services;

use App\Domain\Import\Dto\ImportCreateDto;
use App\Domain\Import\Entity\Import;

interface ImportServiceInterface
{
    public function createImport(ImportCreateDto $dto): Import;
    public function updateImport(Import $dto): Import;
    public function findImport(int $importId): Import;
}