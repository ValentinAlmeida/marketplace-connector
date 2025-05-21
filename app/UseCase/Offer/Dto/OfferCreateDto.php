<?php

namespace App\UseCase\Offer\Dto;

use App\Entities\ValueObjects\Reference;

/**
 * Data Transfer Object for creating an offer.
 *
 * This DTO encapsulates all necessary information required to create a new offer.
 */
final class OfferCreateDto
{
    /**
     * @param Reference $reference Unique reference identifier for the offer.
     * @param string $title Title of the offer.
     * @param string $description Detailed description of the offer.
     * @param string $status Current status of the offer (e.g., active, inactive).
     * @param array $images List of image URLs associated with the offer.
     * @param int $stock Quantity of items available in stock.
     * @param float $price Price of the offer.
     */
    public function __construct(
        public readonly Reference $reference,
        public readonly string $title,
        public readonly string $description,
        public readonly string $status,
        public readonly array $images,
        public readonly int $stock,
        public readonly float $price
    ) {}
}
