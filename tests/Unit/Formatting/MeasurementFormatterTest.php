<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Formatting;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;

final class MeasurementFormatterTest extends TestCase
{
    #[DataProvider('measurements')]
    public function testItFormatsMeasurements(
        int|float $value,
        Unit $unit,
        string $expected
    ): void {
        self::assertSame($expected, MeasurementFormatter::format($value, $unit));
    }

    /**
     * @return iterable<string, array{int|float, Unit, string}>
     */
    public static function measurements(): iterable
    {
        yield 'integer-valued float' => [30.0, Unit::CELSIUS, '30 °C'];
        yield 'decimal float' => [5.2, Unit::METERS_PER_SECOND, '5.2 m/s'];
        yield 'integer' => [1013, Unit::HECTOPASCAL, '1013 hPa'];
        yield 'negative value' => [-2.75, Unit::CELSIUS, '-2.75 °C'];
        yield 'zero' => [0, Unit::PERCENT, '0 %'];
        yield 'full precision' => [5.2345678901234, Unit::MILLIMETER, '5.2345678901234 mm'];
    }

    public function testItPreservesAnUnavailableMeasurement(): void
    {
        self::assertNull(MeasurementFormatter::format(null, Unit::CELSIUS));
    }
}
