<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollutionEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\OneCallEndpoint;
use ProgrammatorDev\OpenWeatherMap\Endpoint\WeatherEndpoint;

class OpenWeatherMapTest extends AbstractTest
{
    public function testOpenWeatherMapGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->givenApi()->getConfig());
    }

    public function testOpenWeatherMapGetOneCall()
    {
        $this->assertInstanceOf(OneCallEndpoint::class, $this->givenApi()->getOneCall());
    }

    public function testOpenWeatherMapGetWeather()
    {
        $this->assertInstanceOf(WeatherEndpoint::class, $this->givenApi()->getWeather());
    }

    public function testOpenWeatherMapGetAirPollution()
    {
        $this->assertInstanceOf(AirPollutionEndpoint::class, $this->givenApi()->getAirPollution());
    }

    public function testOpenWeatherMapGetGeocoding()
    {
        $this->assertInstanceOf(GeocodingEndpoint::class, $this->givenApi()->getGeocoding());
    }
}