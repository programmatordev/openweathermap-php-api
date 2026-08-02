<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution\Forecast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedForecastPeriod(): void
    {
        $response = Fixture::json('air-pollution/forecast/good-to-moderate.json');
        $period = Period::fromArray($response['list'][0]);

        self::assertSame(1785614400, $period->forecastAt()?->getTimestamp());
        self::assertSame('UTC', $period->forecastAt()?->getTimezone()->getName());
        self::assertSame(AirQualityIndex::MODERATE, $period->airQualityIndex());
        self::assertSame(414.72, $period->components()?->carbonMonoxide());
        self::assertSame(36.74, $period->components()?->fineParticulateMatter());
        self::assertSame('36.74 µg/m³', $period->components()?->fineParticulateMatterWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Period::fromArray([]);

        self::assertNull($missing->forecastAt());
        self::assertNull($missing->airQualityIndex());
        self::assertNull($missing->components());

        $period = Period::fromArray([
            'dt' => null,
            'main' => ['aqi' => null, 'unknown' => true],
            'components' => [
                'co' => null,
                'unknown' => new \stdClass(),
            ],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($period->forecastAt());
        self::assertNull($period->airQualityIndex());
        self::assertNull($period->components()?->carbonMonoxide());
        self::assertNull($period->components()?->nitrogenDioxide());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Period::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'forecast time' => [
            ['dt' => '1785614400'],
            '"dt" expected int, string received.',
        ];
        yield 'main' => [
            ['main' => 'invalid'],
            '"main" expected array, string received.',
        ];
        yield 'AQI float' => [
            ['main' => ['aqi' => 1.0]],
            '"main.aqi" expected int, float received.',
        ];
        yield 'unsupported AQI' => [
            ['main' => ['aqi' => 6]],
            '"main.aqi" expected an integer from 1 through 5, "6" received.',
        ];
        yield 'components' => [
            ['components' => 'invalid'],
            '"components" expected array, string received.',
        ];
    }
}
