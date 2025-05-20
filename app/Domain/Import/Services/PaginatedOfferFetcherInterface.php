<?php

namespace App\Domain\Import\Services;

interface PaginatedOfferFetcherInterface
{
    public function fetch(): array;
}
