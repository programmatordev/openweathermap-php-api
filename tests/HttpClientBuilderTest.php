<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpClientBuilderTest extends AbstractTest
{
    private HttpClientBuilder $httpClientBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientBuilder = new HttpClientBuilder();
    }

    public function testHttpClientBuilderGetPlugin()
    {
        $this->httpClientBuilder->addPlugin(
            new HeaderDefaultsPlugin([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
        );
        $this->httpClientBuilder->addPlugin(
            new RetryPlugin([
                'retries' => 3
            ])
        );

        $this->assertInstanceOf(
            HeaderDefaultsPlugin::class,
            $this->httpClientBuilder->getPlugin(HeaderDefaultsPlugin::class)
        );
        $this->assertInstanceOf(
            RetryPlugin::class,
            $this->httpClientBuilder->getPlugin(RetryPlugin::class)
        );
    }

    public function testHttpClientBuilderGetHttpClient()
    {
        $this->assertInstanceOf(HttpMethodsClient::class, $this->httpClientBuilder->getHttpClient());
    }

    public function testHttpClientBuilderGetRequestFactory()
    {
        $this->assertInstanceOf(RequestFactoryInterface::class, $this->httpClientBuilder->getRequestFactory());
    }

    public function testHttpClientBuilderGetStreamFactory()
    {
        $this->assertInstanceOf(StreamFactoryInterface::class, $this->httpClientBuilder->getStreamFactory());
    }
}