<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

class WithCacheTtl extends AbstractTest
{
    public function testWithCacheTtl()
    {
        $this->assertSame(
            60 * 60,
            $this->givenApi()->getWeather()
                ->withCacheTtl(60 * 60)
                ->getCacheTtl()
        );
    }

    public function testWithCacheTtlGetCacheTtl()
    {
        $this->assertSame(60 * 10, $this->givenApi()->getWeather()->getCacheTtl());
    }
}