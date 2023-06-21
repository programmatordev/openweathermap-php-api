<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Geocoding;

class OpenWeatherMapTest extends AbstractTest
{
    public function testOpenWeatherMapGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->getApi()->getConfig());
    }

    public function testOpenWeatherMapGetGeocoding()
    {
        $this->assertInstanceOf(Geocoding::class, $this->getApi()->getGeocoding());
    }
}