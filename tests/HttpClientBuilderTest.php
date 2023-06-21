<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;

class HttpClientBuilderTest extends AbstractTest
{
    private HttpClientBuilder $httpClientBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientBuilder = $this->getApi()
            ->getConfig()
            ->getHttpClientBuilder();
    }

    public function testHttpClientBuilderGetPlugins()
    {
        $this->httpClientBuilder->addPlugin(
            new Plugin\HeaderDefaultsPlugin([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])
        );
        $this->httpClientBuilder->addPlugin(
            new Plugin\RetryPlugin([
                'retries' => 3
            ])
        );

        $plugins = $this->httpClientBuilder->getPlugins();

        $this->assertContainsOnlyInstancesOf(Plugin::class, $plugins);
        $this->assertInstanceOf(Plugin\HeaderDefaultsPlugin::class, $plugins[0]);
        $this->assertInstanceOf(Plugin\RetryPlugin::class, $plugins[1]);
    }

    public function testHttpClientBuilderGetHttpClient()
    {
        $this->assertInstanceOf(HttpMethodsClient::class, $this->httpClientBuilder->getHttpClient());
    }
}