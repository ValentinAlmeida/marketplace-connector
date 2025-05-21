<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;
use App\UseCase\Import\Dto\ImportCreateDto;

interface ICreate
{
    public function execute(ImportCreateDto $dto): Import;
}