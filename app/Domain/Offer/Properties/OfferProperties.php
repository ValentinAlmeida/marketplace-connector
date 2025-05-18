<?php

namespace App\Domain\Offer\Properties;

use App\Domain\Offer\Enums\OfferStatus;
use App\Domain\Shared\ValueObjects\Reference;
use Carbon\Carbon;

final class OfferProperties
{
    public function __construct(
        public readonly Reference $reference,
        public readonly string $title,
        public readonly string $description,
        public readonly OfferStatus $status,
        public readonly int $stock,
        public readonly float $price,
        public readonly ?array $images,
        public readonly ?array $priceHistory,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt
    ) {}
}