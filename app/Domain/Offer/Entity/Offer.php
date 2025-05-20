<?php

namespace App\Domain\Offer\Entity;

use App\Domain\Offer\Dto\OfferCreateDto;
use App\Domain\Offer\Dto\OfferRestoreDto;
use App\Domain\Offer\Properties\OfferProperties;
use App\Domain\Shared\Entity\AbstractEntity;
use App\Domain\Shared\ValueObjects\Identifier;
use Carbon\Carbon;

/**
 * Domain entity representing an Offer.
 *
 * Encapsulates business rules and state for offer-related operations.
 */
final class Offer extends AbstractEntity
{
    private OfferProperties $props;

    /**
     * Private constructor to enforce named constructors for creation and restoration.
     *
     * @param OfferProperties $props Offer value object containing all relevant properties.
     * @param Identifier|null $id Optional unique identifier for the offer.
     */
    private function __construct(OfferProperties $props, ?Identifier $id = null)
    {
        parent::__construct($id);
        $this->props = $props;
    }

    /**
     * Factory method to create a new Offer instance from a DTO.
     *
     * @param OfferCreateDto $dto Data for creating a new offer.
     * @return self
     */
    public static function create(OfferCreateDto $dto): self
    {
        $now = Carbon::now();

        $props = new OfferProperties(
            reference: $dto->reference,
            title: $dto->title,
            description: $dto->description,
            status: $dto->status,
            stock: $dto->stock,
            price: $dto->price,
            images: $dto->images,
            createdAt: $now,
            updatedAt: $now
        );

        return new self($props);
    }

    /**
     * Factory method to rehydrate an existing Offer instance from a DTO and identifier.
     *
     * @param OfferRestoreDto $dto Data for restoring an offer.
     * @param int $id Identifier for the offer.
     * @return self
     */
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
            createdAt: $dto->createdAt,
            updatedAt: $dto->updatedAt
        );

        return new self($props, Identifier::create($id));
    }

    /**
     * Returns the internal OfferProperties value object.
     *
     * @return OfferProperties
     */
    public function getProps(): OfferProperties
    {
        return $this->props;
    }
}
