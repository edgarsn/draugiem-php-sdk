<?php

declare(strict_types=1);

namespace Newman\DraugiemPhpSdk;

use GuzzleHttp\Client;

abstract class AbstractDraugiemApi
{
    /**
     * Get Guzzle Client.
     *
     * @return Client
     */
    abstract protected function getGuzzleClient(): Client;

    /**
     * Get base api url.
     *
     * @return string
     */
    abstract protected function getBaseApiUrl(): string;

    /**
     * Get passport login url.
     *
     * @return string
     */
    abstract protected function getPassportLoginUrl(): string;
}
