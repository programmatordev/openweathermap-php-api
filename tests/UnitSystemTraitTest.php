<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class UnitSystemTraitTest extends AbstractTest
{
    public function testUnitSystemTraitWithUnitSystem()
    {
        $this->assertSame(
            'imperial',
            $this->givenApi()->weather()->withUnitSystem('imperial')->getUnitSystem()
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidUnitSystemData')]
    public function testUnitSystemTraitWithUnitSystemWithInvalidValue(string $unitSystem, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->weather()->withUnitSystem($unitSystem);
    }
}