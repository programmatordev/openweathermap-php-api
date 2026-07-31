<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Enum;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;

final class UnitsTest extends TestCase
{
    /**
     * @param array{Unit, Unit} $expected
     */
    #[DataProvider('unitMappings')]
    public function testItMapsOpenWeatherUnits(
        Units $units,
        array $expected,
    ): void {
        self::assertSame($expected[0], $units->temperatureUnit());
        self::assertSame($expected[1], $units->speedUnit());
    }

    /**
     * @return iterable<string, array{Units, array{Unit, Unit}}>
     */
    public static function unitMappings(): iterable
    {
        yield 'standard' => [
            Units::STANDARD,
            [Unit::KELVIN, Unit::METERS_PER_SECOND],
        ];
        yield 'metric' => [
            Units::METRIC,
            [Unit::CELSIUS, Unit::METERS_PER_SECOND],
        ];
        yield 'imperial' => [
            Units::IMPERIAL,
            [Unit::FAHRENHEIT, Unit::MILES_PER_HOUR],
        ];
    }

    public function testItUsesOpenWeatherQueryValues(): void
    {
        self::assertSame('standard', Units::STANDARD->value);
        self::assertSame('metric', Units::METRIC->value);
        self::assertSame('imperial', Units::IMPERIAL->value);
    }

    public function testMeasurementUnitsExposeDisplaySymbols(): void
    {
        self::assertSame([
            'KELVIN' => 'K',
            'CELSIUS' => '°C',
            'FAHRENHEIT' => '°F',
            'METERS_PER_SECOND' => 'm/s',
            'MILES_PER_HOUR' => 'mph',
            'HECTOPASCAL' => 'hPa',
            'PERCENT' => '%',
            'METER' => 'm',
            'DEGREE' => '°',
            'MILLIMETER' => 'mm',
            'MILLIMETERS_PER_HOUR' => 'mm/h',
            'MICROGRAMS_PER_CUBIC_METER' => 'µg/m³',
        ], array_combine(
            array_column(Unit::cases(), 'name'),
            array_map(
                static fn (Unit $unit): string => $unit->symbol(),
                Unit::cases(),
            ),
        ));
    }
}
