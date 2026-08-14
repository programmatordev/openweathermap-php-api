<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution\Forecast;

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

        self::assertSame(1785614400, $period->dateTime()?->getTimestamp());
        self::assertSame('UTC', $period->dateTime()?->getTimezone()->getName());
        self::assertSame(AirQualityIndex::MODERATE, $period->airQualityIndex());
        self::assertSame(414.72, $period->components()?->carbonMonoxide());
        self::assertSame(36.74, $period->components()?->fineParticulateMatter());
        self::assertSame('36.74 µg/m³', $period->components()?->fineParticulateMatterWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Period::fromArray([]);

        self::assertNull($missing->dateTime());
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

        self::assertNull($period->dateTime());
        self::assertNull($period->airQualityIndex());
        self::assertNull($period->components()?->carbonMonoxide());
        self::assertNull($period->components()?->nitrogenDioxide());
    }

    public function testRejectsInvalidForecastTime(): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage('"dt" expected int, string received.');

        Period::fromArray(['dt' => '1785614400']);
    }
}
