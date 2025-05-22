<?php

namespace App\UseCase\Import;

use App\Events\Import\OfferProcessingFailed;
use App\Events\Import\OfferSuccessfullySentToHub;
use App\UseCase\Contracts\Gateways\IOfferSender;
use App\UseCase\Contracts\Import\IImportProcessor;
use App\UseCase\Contracts\Import\ISendSingleOfferToHub;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SendSingleOfferToHub
 *
 * Use case responsible for preparing an offer's data and sending it to an external system (Hub).
 * It handles different offer data formats, constructs a payload, and manages success or failure by dispatching events.
 */
class SendSingleOfferToHub implements ISendSingleOfferToHub
{
    /**
     * SendSingleOfferToHub constructor.
     *
     * @param IImportProcessor $importService Service for interacting with import data.
     * @param IOfferSender $sender Service responsible for the actual sending of the offer payload.
     */
    public function __construct(
        private IImportProcessor $importService,
        private IOfferSender $sender
    ) {}

    /**
     * Executes the process of preparing and sending a single offer to the Hub.
     *
     * Validates the import status, prepares a payload from the offer data,
     * sends it via the IOfferSender, and dispatches events based on the outcome.
     *
     * @param int $importId The ID of the import process.
     * @param int $originalOfferId The original ID of the offer.
     * @param mixed $offerData The raw offer data, which can be an entity object or an array.
     * @return void
     * @throws \InvalidArgumentException If the offerData format is unexpected or the payload is malformed.
     */
    public function execute(int $importId, int $originalOfferId, mixed $offerData): void
    {
        Log::info("SendSingleOfferToHubUseCase: Started for importId {$importId}, offerId {$originalOfferId}");
        $payloadToSend = [];

        try {
            $import = $this->importService->findImport($importId);
            
            if ($offerData instanceof \App\Entities\Offer) {
                $offerProps = $offerData->getProps();
                $payloadToSend = [
                    'title'       => $offerProps->title,
                    'description' => $offerProps->description,
                    'status'      => $offerProps->status,
                    'stock'       => $offerProps->stock,
                ];
            } elseif (is_array($offerData)) {
                 $payloadToSend = [
                     'title'       => $offerData['title'] ?? null,
                     'description' => $offerData['description'] ?? null,
                     'status'      => $offerData['status'] ?? null,
                     'stock'       => $offerData['stock'] ?? null,
                 ];
            } else {
                 throw new \InvalidArgumentException("Unexpected offerData format for offerId {$originalOfferId}.");
            }
            
            if (empty($payloadToSend) || !isset($payloadToSend['title'])) {
                throw new \InvalidArgumentException("Payload for the hub is empty or malformed for offerId {$originalOfferId}.");
            }

            $this->sender->sendSingle($payloadToSend);
            OfferSuccessfullySentToHub::dispatch($importId, $originalOfferId);
            Log::info("SendSingleOfferToHubUseCase: Success for importId {$importId}, offerId {$originalOfferId}. Event OfferSuccessfullySentToHub dispatched.");

        } catch (Throwable $e) {
            $originalErrorMessage = substr($e->getMessage(), 0, 250);
            $requestBodyJson = json_encode(empty($payloadToSend) ? $offerData : $payloadToSend, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $detailedReason = "Original Error: {$originalErrorMessage}. Sent/Prepared Body: {$requestBodyJson}";

            Log::error("SendSingleOfferToHubUseCase: Failure for importId {$importId}, offerId {$originalOfferId}. Error: {$detailedReason}");
            OfferProcessingFailed::dispatch($importId, $originalOfferId, 'send_to_hub', $detailedReason);
        }
    }
}