<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;

class ConfigTest extends AbstractTest
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->getApi()->getConfig();
    }

    public function testConfigGetApplicationKey()
    {
        $this->assertSame('testappkey', $this->config->getApplicationKey());
    }

    public function testConfigGetHttpClientBuilder()
    {
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
    }
}