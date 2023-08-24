<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class WithUnitSystemTest extends AbstractTest
{
    public function testWithUnitSystem()
    {
        $this->assertSame(
            'imperial',
            $this->givenApi()->getWeather()
                ->withUnitSystem('imperial')
                ->getUnitSystem()
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidUnitSystemData')]
    public function testWithUnitSystemWithInvalidValue(string $unitSystem, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->getWeather()->withUnitSystem($unitSystem);
    }

    public function testWithUnitSystemGetUnitSystem()
    {
        $this->assertSame('metric', $this->givenApi()->getWeather()->getUnitSystem());
    }
}