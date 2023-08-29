<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;

class OpenWeatherMapTest extends AbstractTest
{
    public function testOpenWeatherMapConfig()
    {
        $this->assertInstanceOf(Config::class, $this->givenApi()->config);
    }

    public function testOpenWeatherMapOneCall()
    {
        $this->assertInstanceOf(OneCallEndpoint::class, $this->givenApi()->oneCall);
    }

    public function testOpenWeatherMapWeather()
    {
        $this->assertInstanceOf(WeatherEndpoint::class, $this->givenApi()->weather);
    }

    public function testOpenWeatherMapAirPollution()
    {
        $this->assertInstanceOf(AirPollutionEndpoint::class, $this->givenApi()->airPollution);
    }

    public function testOpenWeatherMapGeocoding()
    {
        $this->assertInstanceOf(GeocodingEndpoint::class, $this->givenApi()->geocoding);
    }
}