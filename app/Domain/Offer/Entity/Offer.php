<?php

namespace App\Domain\Offer\Entity;

use App\Domain\Offer\Dto\OfferCreateDto;
use App\Domain\Offer\Dto\OfferRestoreDto;
use App\Domain\Offer\Enums\OfferStatus;
use App\Domain\Offer\Properties\OfferProperties;
use App\Domain\Shared\Entity\AbstractEntity;
use App\Domain\Shared\ValueObjects\Identifier;
use Carbon\Carbon;

final class Offer extends AbstractEntity
{
    private OfferProperties $props;

    private function __construct(OfferProperties $props, ?Identifier $id = null)
    {
        parent::__construct($id);
        $this->props = $props;
    }

    public static function create(OfferCreateDto $dto): self
    {
        $now = Carbon::now();

        $props = new OfferProperties(
            reference: $dto->reference,
            title: $dto->title,
            description: $dto->description,
            status: OfferStatus::ACTIVE,
            stock: $dto->stock,
            price: $dto->price,
            images: null,
            priceHistory: null,
            createdAt: $now,
            updatedAt: $now
        );

        return new self($props);
    }

    public static function restore(OfferRestoreDto $dto, int $id): self
    {
        $props = new OfferProperties(
            reference: $dto->reference,
            title: $dto->title,
            description: $dto->description,
            status: $dto->status,
            stock: $dto->stock,
            price: $dto->price,
            images: $dto->images,
            priceHistory: $dto->priceHistory,
            createdAt: $dto->createdAt,
            updatedAt: $dto->updatedAt
        );

        return new self($props, Identifier::create($id));
    }

    public function getProps(): OfferProperties
    {
        return $this->props;
    }

    public function getStatusDescription(): string
    {
        return $this->props->status->withMeta()['description'];
    }
}