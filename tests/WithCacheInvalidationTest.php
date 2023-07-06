<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

class WithCacheInvalidationTest extends AbstractTest
{
    public function testWithCacheInvalidation()
    {
        $this->assertSame(
            true,
            $this->getApi()->getWeather()
                ->withCacheInvalidation()
                ->getCacheInvalidation()
        );
    }

    public function testWithCacheInvalidationGetCacheInvalidation()
    {
        $this->assertSame(false, $this->getApi()->getWeather()->getCacheInvalidation());
    }
}