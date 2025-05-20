<?php

namespace App\Domain\Import\Events;

/**
 * Event triggered when an import process has started.
 */
class ImportStarted
{
    /**
     * The ID of the started import.
     *
     * @var int
     */
    public function __construct(public int $importId) {}
}
