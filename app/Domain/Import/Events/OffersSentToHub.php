<?php

namespace App\Domain\Import\Events;

class OffersSentToHub {
    public function __construct(public int $importId) {}
}