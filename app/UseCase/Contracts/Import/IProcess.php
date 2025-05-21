<?php

namespace App\UseCase\Contracts\Import;

interface IProcess
{
    public function execute(int $importId): void;
}