<?php

namespace App\Domain\Import\Services;

use App\Domain\Offer\Entity\Offer;

interface OffersFetcherInterface
{
    /**
     * @param array<int> $offerIds
     * @return array<Offer>
     */
    public function fetch(array $offerIds): array;
}
