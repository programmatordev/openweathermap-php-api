<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use ProgrammatorDev\OpenWeatherMap\Endpoint\AbstractEndpoint;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class AbstractEndpointTest extends AbstractTest
{
    public function testAbstractEndpointWithCache()
    {
        $this->mockHttpClient->addResponse(
            new Response(body: '[]')
        );

        $cache = $this->createMock(CacheItemPoolInterface::class);
        $cache->expects($this->once())->method('save');

        $api = $this->givenApi();
        $api->getConfig()->setCache($cache);

        $this->mockSendRequest($api);
    }

    public function testAbstractEndpointWithLogger()
    {
        $this->mockHttpClient->addResponse(
            new Response(body: '[]')
        );

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeastOnce())->method('info');

        $api = $this->givenApi();
        $api->getConfig()->setLogger($logger);

        $this->mockSendRequest($api);
    }

    private function mockSendRequest(OpenWeatherMap $api): void
    {
        // Using ReflectionClass to be able to call the *protected* sendRequest method
        // (otherwise it would not be possible)
        $endpoint = new AbstractEndpoint($api);
        $reflectionClass = new \ReflectionClass($endpoint);
        $sendRequest = $reflectionClass->getMethod('sendRequest');
        $sendRequest->invokeArgs($endpoint, ['GET', 'https://mock.test']);
    }
}