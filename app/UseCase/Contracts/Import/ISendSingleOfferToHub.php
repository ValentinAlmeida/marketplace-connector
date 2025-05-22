<?php

namespace App\UseCase\Contracts\Import;

/**
 * Interface ISendSingleOfferToHub
 *
 * Defines the contract for a use case responsible for sending the data
 * of a single offer to an external system (Hub) as part of an import process.
 */
interface ISendSingleOfferToHub
{
    /**
     * Executes the process of sending a single offer's data to the Hub.
     *
     * Implementations are expected to handle the transmission of the offer data.
     * The outcome, such as successful sending or any failure, is typically
     * communicated through events or by updating relevant entities, as this method returns void.
     *
     * @param int $importId The ID of the import process to which this offer is related.
     * @param int $originalOfferId The original ID of the offer that is being sent.
     * @param mixed $offerData The data payload of the offer to be sent.
     * @return void
     */
    public function execute(int $importId, int $originalOfferId, mixed $offerData): void;
}