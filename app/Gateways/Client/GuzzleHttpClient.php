<?php

namespace App\Gateways\Client;

use App\UseCase\Contracts\Gateways\IHttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Class GuzzleHttpClient
 *
 * An implementation of the IHttpClient interface using the Guzzle HTTP Client.
 * It includes a default timeout and a retry mechanism for requests.
 */
class GuzzleHttpClient implements IHttpClient
{
    private Client $client;

    /**
     * GuzzleHttpClient constructor.
     *
     * Initializes a new GuzzleHttp Client instance with a default timeout.
     */
    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5.0,
        ]);
    }

    /**
     * Sends a GET request to the specified URL.
     *
     * @param string $url The URL to send the GET request to.
     * @param array<string, mixed> $options Associative array of query parameters or other request options.
     * @return ResponseInterface The PSR-7 response.
     * @throws RuntimeException If the request fails after all retry attempts.
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        return $this->requestWithRetry('GET', $url, ['query' => $options]);
    }

    /**
     * Sends a POST request to the specified URL with JSON data.
     *
     * @param string $url The URL to send the POST request to.
     * @param array<string, mixed> $data Associative array of data to be sent as JSON in the request body.
     * @return ResponseInterface The PSR-7 response.
     * @throws RuntimeException If the request fails after all retry attempts.
     */
    public function post(string $url, array $data = []): ResponseInterface
    {
        return $this->requestWithRetry('POST', $url, ['json' => $data]);
    }

    /**
     * Makes an HTTP request with a specified number of retries on failure.
     *
     * @param string $method The HTTP method (e.g., 'GET', 'POST').
     * @param string $url The URL for the request.
     * @param array<string, mixed> $options Guzzle request options.
     * @param int $retries The number of times to retry the request upon failure.
     * @param int $delayMs The delay in milliseconds between retry attempts.
     * @return ResponseInterface The PSR-7 response.
     * @throws RuntimeException If the request fails after all attempts or if an unexpected state is reached.
     */
    private function requestWithRetry(string $method, string $url, array $options, int $retries = 3, int $delayMs = 100): ResponseInterface
    {
        for ($i = 0; $i < $retries; $i++) {
            try {
                return $this->client->request($method, $url, $options);
            } catch (GuzzleException $e) {
                if ($i === $retries - 1) {
                    throw new RuntimeException("HTTP {$method} to {$url} failed after {$retries} attempts.", 0, $e);
                }
                usleep($delayMs * 1000);
            }
        }
        
        throw new RuntimeException("Unreachable code in requestWithRetry after loop completion.");
    }
}