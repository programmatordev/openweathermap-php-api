<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\AirPollution;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Geocoding;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Weather;

class OpenWeatherMapTest extends AbstractTest
{
    public function testOpenWeatherMapGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->getApi()->getConfig());
    }

    public function testOpenWeatherMapGetWeather()
    {
        $this->assertInstanceOf(Weather::class, $this->getApi()->getWeather());
    }

    public function testOpenWeatherMapGetAirPollution()
    {
        $this->assertInstanceOf(AirPollution::class, $this->getApi()->getAirPollution());
    }

    public function testOpenWeatherMapGetGeocoding()
    {
        $this->assertInstanceOf(Geocoding::class, $this->getApi()->getGeocoding());
    }
}