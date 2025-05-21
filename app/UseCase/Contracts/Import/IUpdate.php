<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;

interface IUpdate
{
    public function execute(Import $import): Import;
}