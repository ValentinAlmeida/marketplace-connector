<?php

namespace App\Domain\Import\Events;

class ImportStarted {
    public function __construct(public int $importId) {}
}