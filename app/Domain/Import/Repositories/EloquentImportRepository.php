<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Import\Adapters\ImportAdapter;
use App\Domain\Import\Entity\Import;
use App\Domain\Import\Repositories\ImportRepositoryInterface;
use App\Models\Import as ImportModel;
use DomainException;

class EloquentImportRepository implements ImportRepositoryInterface
{
    public function create(Import $import): Import
    {
        $model = ImportModel::create(ImportAdapter::toModel($import));
        return ImportAdapter::toEntity($model);
    }

    public function update(Import $import): Import
    {
        $model = ImportModel::findOrFail($import->getIdentifier()->value());
        $model->update(ImportAdapter::toModel($import));
        return ImportAdapter::toEntity($model);
    }

    public function findById(int $id): Import
    {
        $model = ImportModel::find($id);

        throw_if(!$model, new \DomainException('Importação não encontrada'));
        
        return ImportAdapter::toEntity($model);
    }

    public function listImports(array $filters = []): array
    {
        $query = ImportModel::query();
        
        return $query->get()
            ->map(fn (ImportModel $model) => ImportAdapter::toEntity($model))
            ->toArray();
    }
}