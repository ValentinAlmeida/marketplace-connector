<?php

namespace App\UseCase\Contracts\Import;

use App\Entities\Import;

interface ISchedule
{
    public function execute(Import $import): void;
}