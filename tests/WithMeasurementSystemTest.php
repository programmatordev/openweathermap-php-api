<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class WithMeasurementSystemTest extends AbstractTest
{
    public function testWithMeasurementSystem()
    {
        $this->assertSame('metric', $this->getApi()->getWeather()->getMeasurementSystem());

        $this->assertSame(
            'imperial',
            $this->getApi()->getWeather()
                ->withMeasurementSystem('imperial')
                ->getMeasurementSystem()
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidMeasurementSystemData')]
    public function testWithMeasurementSystemWithInvalidValue(string $measurementSystem, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->getApi()->getWeather()->withMeasurementSystem($measurementSystem);
    }
}