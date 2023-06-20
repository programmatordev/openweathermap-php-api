<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

class AbstractTestCase extends TestCase
{
    protected Client $mockHttpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHttpClient = new Client();
    }

    protected function getApi(): OpenWeatherMap
    {
        return new OpenWeatherMap(
            new Config([
                'applicationKey' => 'fakeappkey',
                'httpClientBuilder' => new HttpClientBuilder($this->mockHttpClient)
            ])
        );
    }
}