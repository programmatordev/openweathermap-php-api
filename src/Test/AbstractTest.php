<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Http\Mock\Client;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

class AbstractTest extends TestCase
{
    protected const APPLICATION_KEY = 'testappkey';

    protected Client $mockHttpClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHttpClient = new Client();
    }

    protected function givenApi(): OpenWeatherMap
    {
        return new OpenWeatherMap(
            new Config([
                'applicationKey' => self::APPLICATION_KEY,
                'httpClientBuilder' => new HttpClientBuilder($this->mockHttpClient)
            ])
        );
    }
}