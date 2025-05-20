<?php

namespace App\Domain\Offer\Dto;

use App\Domain\Shared\ValueObjects\Reference;
use Carbon\Carbon;

/**
 * Data Transfer Object for restoring an offer from persistent storage.
 *
 * This DTO encapsulates all relevant data to rehydrate an offer entity.
 */
final class OfferRestoreDto
{
    /**
     * @param Reference $reference Unique reference identifier for the offer.
     * @param string $title Title of the offer.
     * @param string $description Detailed description of the offer.
     * @param string $status Current status of the offer (e.g., active, inactive).
     * @param int $stock Quantity of items available in stock.
     * @param float $price Price of the offer.
     * @param array|null $images Optional list of image URLs associated with the offer.
     * @param Carbon $createdAt Date and time when the offer was created.
     * @param Carbon $updatedAt Date and time when the offer was last updated.
     */
    public function __construct(
        public readonly Reference $reference,
        public readonly string $title,
        public readonly string $description,
        public readonly string $status,
        public readonly int $stock,
        public readonly float $price,
        public readonly ?array $images,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt
    ) {}
}
