<?php

namespace App\UseCase\Contracts\Gateways;

use App\Entities\Import;

/**
 * Interface OfferHubSenderInterface
 *
 * Defines the contract for sending offer data to an external hub or service.
 */
interface IOfferSender
{
    /**
     * Send the given offers to the hub associated with the import.
     *
     * @param array $offers List of offer data to be sent.
     * @param Import $import The import entity associated with the offers.
     * @return Import The updated import entity after sending the offers.
     */
    public function send(array $offers, Import $import): Import;
}
