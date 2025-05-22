<?php

namespace App\UseCase\Contracts\Gateways;

/**
 * Interface IOfferSender
 *
 * Defines the contract for services responsible for sending offer data to an external system or hub.
 */
interface IOfferSender
{
    /**
     * Sends a single offer payload.
     *
     * Implementations should handle the actual transmission of the offer data.
     * They may throw exceptions if the sending process fails.
     *
     * @param array $offerPayload The data payload of the offer to be sent.
     * @return void
     */
    public function sendSingle(array $offerPayload): void;
}