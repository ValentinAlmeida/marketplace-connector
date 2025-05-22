<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;
use App\UseCase\Import\Dto\ImportCreateDto;

/**
 * Interface ICreate
 *
 * Defines the contract for a use case responsible for creating a new import.
 */
interface ICreate
{
    /**
     * Executes the import creation process.
     *
     * @param ImportCreateDto $dto Data Transfer Object containing the necessary data to create a new import.
     * @return Import The newly created Import entity.
     */
    public function execute(ImportCreateDto $dto): Import;
}