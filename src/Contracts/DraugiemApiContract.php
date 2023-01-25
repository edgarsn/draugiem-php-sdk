<?php

declare(strict_types=1);

namespace Newman\DraugiemPhpSdk\Contracts;

use Psr\Http\Message\ResponseInterface;

interface DraugiemApiContract
{
    /**
     * Inner function that calls Draugiem.lv API and returns response.
     *
     * @param string $action API action that has to be called.
     * @param array<string, mixed> $args Key/value pairs of additional parameters for the request (excluding app, apikey and action).
     * @param string $method HTTP Method. GET or POST.
     * @param array<string, mixed> $guzzleOptions Additional Guzzle options.
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function apiCall(string $action, array $args = [], string $method = 'GET', array $guzzleOptions = []): ResponseInterface;
}
