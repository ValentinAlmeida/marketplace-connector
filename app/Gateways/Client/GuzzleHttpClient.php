<?php

namespace App\Gateways\Client;

use App\UseCase\Contracts\Gateways\IHttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class GuzzleHttpClient implements IHttpClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5.0,
        ]);
    }

    public function get(string $url, array $options = []): ResponseInterface
    {
        return $this->requestWithRetry('GET', $url, ['query' => $options]);
    }

    public function post(string $url, array $data = []): ResponseInterface
    {
        return $this->requestWithRetry('POST', $url, ['json' => $data]);
    }

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

        throw new RuntimeException("Unreachable code.");
    }
}
