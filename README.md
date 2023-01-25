# Draugiem.lv API library for PHP

**This is not the official library!**

This is a minor rewrite of original api library using Guzzle client and strict typing.

## Requirements
- PHP 7.4+

## Installation
Require the package via Composer:

```bash
composer require newman/draugiem-php-sdk
```

# :book: Documentation & Usage

### Make an API call

Response is an implementation of `Psr\Http\Message\ResponseInterface`.

```php
$draugiemApi = new \Newman\DraugiemPhpSdk\DraugiemApi(1234, 'app key');
//$draugiemApi = new \Newman\DraugiemPhpSdk\DraugiemApi(1234, 'app key', 'user key');

$response = $draugiemApi->apiCall('authorize', ['code' => 5728195]);

var_dump($response->getStatusCode());
var_dump($response->getBody()->getContents());
```

### Set default Guzzle client options

```php
$draugiemApi = new \Newman\DraugiemPhpSdk\DraugiemApi(1234, 'app key');
$draugiemApi->setDefaultGuzzleClientOptions([
    'timeout' => 10,
    'headers' => [
        'User-Agent' => 'testing/1.0',
    ],
]);

// make an api call
```

# :handshake: Contributing

We'll appreciate your collaboration to this package.

When making pull requests, make sure:
* All tests are passing: `composer test`
* Test coverage is not reduced: `composer test-coverage`
* There are no PHPStan errors: `composer phpstan`
* Coding standard is followed: `composer lint` or `composer fix-style` to automatically fix it. 
