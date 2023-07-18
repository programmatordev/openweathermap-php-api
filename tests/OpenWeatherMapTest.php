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
        $this->assertInstanceOf(Config::class, $this->getApi()->getConfig());
    }

    public function testOpenWeatherMapGetOneCall()
    {
        $this->assertInstanceOf(OneCallEndpoint::class, $this->getApi()->getOneCall());
    }

    public function testOpenWeatherMapGetWeather()
    {
        $this->assertInstanceOf(WeatherEndpoint::class, $this->getApi()->getWeather());
    }

    public function testOpenWeatherMapGetAirPollution()
    {
        $this->assertInstanceOf(AirPollutionEndpoint::class, $this->getApi()->getAirPollution());
    }

    public function testOpenWeatherMapGetGeocoding()
    {
        $this->assertInstanceOf(GeocodingEndpoint::class, $this->getApi()->getGeocoding());
    }
}