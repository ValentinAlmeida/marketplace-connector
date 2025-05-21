<?php

namespace App\Events\Import;

/**
 * Event triggered when an import process has started.
 */
class Started
{
    /**
     * The ID of the started import.
     *
     * @var int
     */
    public function __construct(public int $importId) {}
}
