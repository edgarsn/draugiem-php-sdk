<?php

declare(strict_types=1);

namespace Newman\DraugiemPhpSdk\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Newman\DraugiemPhpSdk\DraugiemApi;

class DraugiemApiTest extends TestCase
{
    public function test_imageForSize(): void
    {
        $client = $this->getClient();
        $img = 'https://i5.ifrype.com/profile/123/456/v1617882141/sm_123456.jpg';

        $this->assertEquals('https://i5.ifrype.com/profile/123/456/v1617882141/i_123456.jpg', $client->imageForSize($img, 'icon'));
        $this->assertEquals('https://i5.ifrype.com/profile/123/456/v1617882141/sm_123456.jpg', $client->imageForSize($img, 'small'));
        $this->assertEquals('https://i5.ifrype.com/profile/123/456/v1617882141/m_123456.jpg', $client->imageForSize($img, 'medium'));
        $this->assertEquals('https://i5.ifrype.com/profile/123/456/v1617882141/l_123456.jpg', $client->imageForSize($img, 'large'));

        $this->assertEquals('https://i5.ifrype.com/profile/123/456/v1617882141/sm_123456.jpg', $client->imageForSize($img, 'aaaaaa'));
    }

    public function test_getLoginUrl(): void
    {
        $client = $this->getClient();

        $this->assertEquals(
            'https://api.draugiem.lv/authorize/?app=1&hash=7b78ff9505894691a412d7e96cc1253d&redirect=http%3A%2F%2Flocalhost%3Fx%3D1',
            $client->getLoginUrl('http://localhost?x=1')
        );
    }

    public function test_apiCallWithGet(): void
    {
        $client = $this->getClientWithUserKey();

        $fakeResponse = [
            'apikey' => '09797abfe8e5ea53fd857cc372e3a6f5',
            'uid' => 491171,
            'language' => 'lv',
            'users' => [
                491171 => [
                    'name' => 'Jānis',
                    'surname' => 'Bērziņš',
                ],
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], serialize($fakeResponse)),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client->setDefaultGuzzleClientOptions([
            'handler' => $handlerStack,
        ]);

        $response = $client->apiCall('authorize', ['code' => 1234]);

        $request = $mock->getLastRequest();

        $this->assertNotNull($request);
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('app=abc&action=authorize&apikey=def&code=1234', $request->getUri()->getQuery());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($fakeResponse, unserialize($response->getBody()->getContents()));
    }

    public function test_apiCallWithPost(): void
    {
        $client = $this->getClientWithUserKey();

        $fakeResponse = [
            'saved' => 1,
        ];

        $mock = new MockHandler([
            new Response(200, [], serialize($fakeResponse)),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $client->setDefaultGuzzleClientOptions([
            'handler' => $handlerStack,
        ]);

        $response = $client->apiCall('somepost', ['text' => 'Nice'], 'POST');

        $request = $mock->getLastRequest();

        $this->assertNotNull($request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('app=abc&action=somepost&apikey=def&text=Nice', $request->getBody()->getContents());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($fakeResponse, unserialize($response->getBody()->getContents()));
    }

    protected function getClient(): DraugiemApi
    {
        return new DraugiemApi(1, 'abc');
    }

    protected function getClientWithUserKey(): DraugiemApi
    {
        return new DraugiemApi(1, 'abc', 'def');
    }
}
