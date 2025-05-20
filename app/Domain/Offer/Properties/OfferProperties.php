<?php

namespace App\Domain\Offer\Properties;

use App\Domain\Offer\Enums\OfferStatus;
use App\Domain\Shared\ValueObjects\Reference;
use Carbon\Carbon;

/**
 * Value object that encapsulates all properties of an Offer.
 *
 * This class is immutable and represents the state of an offer at a given point in time.
 */
final class OfferProperties
{
    /**
     * @param Reference   $reference   Unique reference identifier for the offer.
     * @param string      $title       Title or name of the offer.
     * @param string      $description Description of the offer.
     * @param string      $status      Current status of the offer (e.g., active, inactive).
     * @param int         $stock       Available stock quantity for the offer.
     * @param float       $price       Price of the offer.
     * @param array|null  $images      Optional list of image URLs associated with the offer.
     * @param Carbon      $createdAt   Timestamp of when the offer was created.
     * @param Carbon      $updatedAt   Timestamp of the last update to the offer.
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
