<?php

namespace App\UseCase\Contracts\Gateways;

use Psr\Http\Message\ResponseInterface;

interface IHttpClient
{
    /**
     * Send a GET request.
     *
     * @param string $url
     * @param array<string, mixed> $options
     * @return ResponseInterface
     */
    public function get(string $url, array $options = []): ResponseInterface;

    /**
     * Send a POST request.
     *
     * @param string $url
     * @param array<string, mixed> $data
     * @return ResponseInterface
     */
    public function post(string $url, array $data = []): ResponseInterface;
}
