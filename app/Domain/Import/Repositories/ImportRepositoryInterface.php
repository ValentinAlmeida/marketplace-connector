<?php

namespace App\Domain\Import\Repositories;

use App\Domain\Import\Entity\Import;

interface ImportRepositoryInterface
{
    public function create(Import $import): Import;
    public function update(Import $import): Import;
    public function findById(int $id): Import;
    public function listImports(array $filters = []): array;
}