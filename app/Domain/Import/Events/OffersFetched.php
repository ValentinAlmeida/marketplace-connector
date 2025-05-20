<?php

namespace App\Domain\Import\Events;

class OffersFetched {
    public function __construct(public int $importId, public array $offers) {}
}