<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

class AbstractEndpointTest extends AbstractTest
{
    public function testAbstractEndpointWithCacheTtl()
    {
        $this->assertSame(
            60 * 60,
            $this->getApi()->getWeather()
                ->withCacheTtl(60 * 60)
                ->getCacheTtl()
        );
    }

    public function testAbstractEndpointGetCacheTtl()
    {
        $this->assertSame(60 * 10, $this->getApi()->getWeather()->getCacheTtl());
    }
}