<?php

namespace App\Domain\Import\Events;

class OfferIdsFetched {
    public function __construct(public int $importId, public array $offerIds) {}
}