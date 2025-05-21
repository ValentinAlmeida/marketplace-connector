<?php

namespace App\UseCase\Contracts\Gateways;

interface IOfferFetcher
{
    public function fetch(array $offerIds): array;
}
