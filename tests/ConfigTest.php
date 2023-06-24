<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidApplicationKeyException;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ConfigTest extends AbstractTest
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config([
            'applicationKey' => 'testappkey'
        ]);
    }

    public function testConfigGetApplicationKey()
    {
        $this->assertSame('testappkey', $this->config->getApplicationKey());
    }

    public function testConfigRequiredApplicationKey()
    {
        $this->expectException(MissingOptionsException::class);

        new Config();
    }

    public function testConfigEmptyApplicationKey()
    {
        $this->expectException(InvalidApplicationKeyException::class);

        new Config([
            'applicationKey' => ''
        ]);
    }

    public function testConfigGetHttpClientBuilder()
    {
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
    }
}