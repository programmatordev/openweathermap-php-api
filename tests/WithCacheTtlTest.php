<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

class WithCacheTtlTest extends AbstractTest
{
    public function testWithCacheTtl()
    {
        $this->assertSame(
            60 * 60,
            $this->givenApi()->weather->withCacheTtl(60 * 60)->getCacheTtl()
        );
    }
}