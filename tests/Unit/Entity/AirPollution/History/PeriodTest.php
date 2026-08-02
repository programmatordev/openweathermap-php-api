<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution\History;

use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Period::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'observation time' => [
            ['dt' => '1782864000'],
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
