<?php

namespace App\Domain\Offer\Dto;

use App\Domain\Shared\ValueObjects\Reference;

final class OfferCreateDto
{
    public function __construct(
        public readonly Reference $reference,
        public readonly string $title,
        public readonly string $description,
        public readonly int $stock,
        public readonly float $price
    ) {}
}