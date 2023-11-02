<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;

class OpenWeatherMapTest extends AbstractTest
{
    #[DataProvider('provideMethodsData')]
    public function testOpenWeatherMapMethods(string $instance, string $methodName)
    {
        $this->assertInstanceOf($instance, $this->givenApi()->$methodName());
    }

    public static function provideMethodsData(): \Generator
    {
        yield 'config' => [Config::class, 'config'];
        yield 'air pollution' => [AirPollutionEndpoint::class, 'airPollution'];
        yield 'geocoding' => [GeocodingEndpoint::class, 'geocoding'];
        yield 'one call' => [OneCallEndpoint::class, 'oneCall'];
        yield 'weather' => [WeatherEndpoint::class, 'weather'];
    }
}