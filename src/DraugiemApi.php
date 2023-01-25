<?php

declare(strict_types=1);

namespace Newman\DraugiemPhpSdk;

use GuzzleHttp\Client;
use Newman\DraugiemPhpSdk\Contracts\DraugiemApiContract;
use Psr\Http\Message\ResponseInterface;

class DraugiemApi extends AbstractDraugiemApi implements DraugiemApiContract
{
    /**
     * @var int $appId
     */
    protected $appId;

    /**
     * @var string $appKey ;
     */
    protected $appKey;

    /**
     * @var string|null $userKey
     */
    protected $userKey;

    /**
     * @var array<string, mixed> $defaultGuzzleOptions
     */
    protected $defaultGuzzleOptions = [];

    public function __construct(int $appId, string $appKey, ?string $userKey = null)
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->userKey = $userKey;

        $this->defaultGuzzleOptions = [
            'timeout' => $this->getDefaultTimeout(),
            'connect_timeout' => $this->getDefaultConnectTimeout(),
        ];
    }

    /**
     * Get user profile image URL with different size.
     *
     * @param string $img User profile image URL from API (default size)
     * @param string $size Desired image size (icon/small/medium/large)
     * @return string
     */
    public function imageForSize(string $img, string $size): string
    {
        $sizes = [
            'icon' => 'i_', //50x50px
            'small' => 'sm_', //100x100px (default)
            'medium' => 'm_', //215px wide
            'large' => 'l_', //710px wide
        ];

        if (isset($sizes[$size])) {
            $img = str_replace('/sm_', '/' . $sizes[$size], $img);
        }

        return $img;
    }

    /**
     * Get URL for Draugiem.lv Passport or Draugiem ID login page to authenticate user
     *
     * @param string $redirect_url URL where user has to be redirected after authorization. The URL has to be in the same domain as URL that has been set in the properties of the application.
     * @return string
     */
    public function getLoginUrl(string $redirect_url): string
    {
        $hash = md5($this->appKey . $redirect_url);

        $link = $this->getPassportLoginUrl() . '?app=' . $this->appId . '&hash=' . $hash . '&redirect=' . urlencode($redirect_url);

        return $link;
    }

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
    public function apiCall(string $action, array $args = [], string $method = 'GET', array $guzzleOptions = []): ResponseInterface
    {
        $client = $this->getGuzzleClient();
        $baseUrl = $this->getBaseApiUrl();
        $method = strtoupper($method);

        if ($method == 'POST') {
            if (!isset($guzzleOptions['form_params']) || !is_array($guzzleOptions['form_params'])) {
                $guzzleOptions['form_params'] = [];
            }

            $guzzleOptions['form_params']['app'] = $this->appKey;
            $guzzleOptions['form_params']['action'] = $action;

            if ($this->userKey) {
                $guzzleOptions['form_params']['apikey'] = $this->userKey;
            }

            $guzzleOptions['form_params'] += $args;

            $response = $client->post($baseUrl, $guzzleOptions);
        } else {
            if (!isset($guzzleOptions['query']) || !is_array($guzzleOptions['query'])) {
                $guzzleOptions['query'] = [];
            }

            $guzzleOptions['query']['app'] = $this->appKey;
            $guzzleOptions['query']['action'] = $action;

            if ($this->userKey) {
                $guzzleOptions['query']['apikey'] = $this->userKey;
            }

            foreach ($args as $argName => $argValue) {
                $guzzleOptions['query'][$argName] = $argValue;
            }

            $response = $client->get($baseUrl, $guzzleOptions);
        }

        return $response;
    }

    /**
     * Set default guzzle client options.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setDefaultGuzzleClientOptions(array $config): void
    {
        $this->defaultGuzzleOptions = $config;
    }

    /**
     * Get Guzzle Client.
     *
     * @return Client
     */
    protected function getGuzzleClient(): Client
    {
        return new Client($this->defaultGuzzleOptions);
    }

    /**
     * Get base api url.
     *
     * @return string
     */
    protected function getBaseApiUrl(): string
    {
        return 'https://api.draugiem.lv/php/';
    }

    /**
     * Get passport login url.
     *
     * @return string
     */
    protected function getPassportLoginUrl(): string
    {
        return 'https://api.draugiem.lv/authorize/';
    }

    /**
     * Default timeout.
     *
     * @link https://docs.guzzlephp.org/en/stable/request-options.html#connect-timeout
     * @return float
     */
    protected function getDefaultConnectTimeout(): float
    {
        return 5;
    }

    /**
     * Default timeout.
     *
     * @link https://docs.guzzlephp.org/en/stable/request-options.html#timeout
     * @return float
     */
    protected function getDefaultTimeout(): float
    {
        return 5;
    }
}
