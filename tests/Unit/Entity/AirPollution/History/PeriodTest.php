<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution\History;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\History\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedHistoricalPeriod(): void
    {
        $response = Fixture::json('air-pollution/history/success.json');
        $period = Period::fromArray($response['list'][0]);

        self::assertSame(1782864000, $period->observedAt()?->getTimestamp());
        self::assertSame('UTC', $period->observedAt()?->getTimezone()->getName());
        self::assertSame(AirQualityIndex::FAIR, $period->airQualityIndex());
        self::assertSame(80.71, $period->components()?->carbonMonoxide());
        self::assertSame(6.86, $period->components()?->fineParticulateMatter());
        self::assertSame('6.86 µg/m³', $period->components()?->fineParticulateMatterWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Period::fromArray([]);

        self::assertNull($missing->observedAt());
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

        self::assertNull($period->observedAt());
        self::assertNull($period->airQualityIndex());
        self::assertNull($period->components()?->carbonMonoxide());
        self::assertNull($period->components()?->nitrogenDioxide());
    }

    public function testRejectsInvalidObservationTime(): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage('"dt" expected int, string received.');

        Period::fromArray(['dt' => '1782864000']);
    }
}
