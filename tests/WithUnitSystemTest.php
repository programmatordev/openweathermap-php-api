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
            $this->getApi()->getWeather()
                ->withUnitSystem('imperial')
                ->getUnitSystem()
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidUnitSystemData')]
    public function testWithUnitSystemWithInvalidValue(string $unitSystem, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getWeather()->withUnitSystem($unitSystem);
    }

    public function testWithUnitSystemGetUnitSystem()
    {
        $this->assertSame('metric', $this->getApi()->getWeather()->getUnitSystem());
    }
}