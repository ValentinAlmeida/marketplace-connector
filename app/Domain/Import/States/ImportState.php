<?php

namespace App\Domain\Import\States;

use App\Domain\Import\Entity\Import;

interface ImportState
{
    public function startProcessing(Import $import): void;
    public function complete(Import $import): void;
    public function fail(Import $import, string $error): void;
    public function cancel(Import $import): void;
    
    public function getStatus(): string;
    public function getHumanStatus(): string;
}