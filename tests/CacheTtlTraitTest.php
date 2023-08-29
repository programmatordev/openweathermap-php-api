<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

class CacheTtlTraitTest extends AbstractTest
{
    public function testCacheTtlTraitWithCacheTtl()
    {
        $this->assertSame(
            60 * 60,
            $this->givenApi()->weather()->withCacheTtl(60 * 60)->getCacheTtl()
        );
    }
}