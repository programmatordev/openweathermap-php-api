<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Request\Stations;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Weather;

final class WeatherTest extends TestCase
{
    public function testMapsAvailableWeatherValues(): void
    {
        $weather = new Weather(
            precipitation: ' RA ',
            descriptor: ' SH ',
            intensity: ' - ',
            proximity: ' VC ',
            obscuration: ' BR ',
            other: ' SQ ',
        );

        self::assertSame('RA', $weather->precipitation());
        self::assertSame('SH', $weather->descriptor());
        self::assertSame('-', $weather->intensity());
        self::assertSame('VC', $weather->proximity());
        self::assertSame('BR', $weather->obscuration());
        self::assertSame('SQ', $weather->other());
        self::assertSame([
            'precipitation' => 'RA',
            'descriptor' => 'SH',
            'intensity' => '-',
            'proximity' => 'VC',
            'obscuration' => 'BR',
            'other' => 'SQ',
        ], $weather->toArray());
    }

    public function testOmitsUnavailableWeatherValues(): void
    {
        $weather = new Weather(precipitation: 'SN');

        self::assertNull($weather->descriptor());
        self::assertSame(['precipitation' => 'SN'], $weather->toArray());
    }

    public function testRejectsWeatherWithoutValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The weather values must not be empty.',
        );

        new Weather();
    }

    public function testRejectsABlankWeatherValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The weather precipitation must be a non-empty string.',
        );

        new Weather(precipitation: '   ');
    }
}
