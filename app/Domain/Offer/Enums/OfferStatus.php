<?php

namespace App\Domain\Offer\Enums;

enum OfferStatus: string
{
    case ACTIVE = 'offer.status.active';
    case INACTIVE = 'offer.status.inactive';
    case SOLD_OUT = 'offer.status.sold_out';
    case EXPIRED = 'offer.status.expired';

    public function withMeta(): array
    {
        return match ($this) {
            self::ACTIVE => ['description' => 'Ativa'],
            self::INACTIVE => ['description' => 'Inativa'],
            self::SOLD_OUT => ['description' => 'Esgotada'],
            self::EXPIRED => ['description' => 'Expirada'],
        };
    }
}