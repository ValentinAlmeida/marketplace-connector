<?php

namespace App\Gateways\Offer\Factories;

use App\Gateways\Offer\Parsing\PaginatedApiResponseParser;
use App\Gateways\Offer\Providers\ApiPageProvider;
use App\UseCase\Contracts\Gateways\IHttpClient;
use App\UseCase\Import\Config\ImportConfig;

/**
 * Class ApiPageProviderFactory
 *
 * Factory responsible for creating instances of ApiPageProvider.
 * It injects the necessary dependencies (configuration, HTTP client, and response parser)
 * into the ApiPageProvider.
 */
class ApiPageProviderFactory
{
    /**
     * ApiPageProviderFactory constructor.
     *
     * @param ImportConfig $config The configuration object for import processes.
     * @param IHttpClient $httpClient The HTTP client instance for making API requests.
     * @param PaginatedApiResponseParser $responseParser The parser for API page responses.
     */
    public function __construct(
        private ImportConfig $config,
        private IHttpClient $httpClient,
        private PaginatedApiResponseParser $responseParser
    ) {}

    /**
     * Creates a new instance of ApiPageProvider.
     *
     * @return ApiPageProvider A new ApiPageProvider instance configured with the factory's dependencies.
     */
    public function create(): ApiPageProvider
    {
        return new ApiPageProvider($this->config, $this->httpClient, $this->responseParser);
    }
}