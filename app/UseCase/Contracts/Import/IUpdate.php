<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;

/**
 * Interface IUpdate
 *
 * Defines the contract for a use case responsible for updating an existing import.
 */
interface IUpdate
{
    /**
     * Executes the import update process.
     *
     * @param Import $import The Import entity containing the updated data.
     * @return Import The updated Import entity.
     */
    public function execute(Import $import): Import;
}