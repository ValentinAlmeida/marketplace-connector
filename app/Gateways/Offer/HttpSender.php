<?php

namespace App\Gateways\Offer;

use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Contracts\Gateways\IOfferSender;
use App\UseCase\Import\Config\ImportConfig;
use RuntimeException;

/**
 * Class HttpSender
 *
 * Implements the IOfferSender interface to send offer data to an external system (Hub)
 * via HTTP POST requests.
 */
class HttpSender implements IOfferSender
{
    /**
     * HttpSender constructor.
     *
     * @param ImportConfig $config The configuration for the import process, including endpoint URLs.
     * @param IHttpClient $httpClient The HTTP client used to make requests.
     */
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient
    ) {}

    /**
     * Sends a single offer payload to the configured Hub endpoint.
     *
     * @param array $offerPayload The data payload of the offer to be sent.
     * @return void
     * @throws RuntimeException If the HTTP request fails or the Hub returns an unexpected status code.
     */
    public function sendSingle(array $offerPayload): void
    {
        $baseUrl = $this->config->getHubCreateEndpoint();
        
        $response = $this->httpClient->post($baseUrl, $offerPayload);

        if ($response->getStatusCode() !== 201) {
            $bodyContent = $response->getBody()->getContents();
            throw new RuntimeException("Error sending offer to the hub. Status: {$response->getStatusCode()}. Response: {$bodyContent}");
        }
    }
}