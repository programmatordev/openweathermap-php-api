<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class ForecastTest extends TestCase
{
    #[DataProvider('capturedForecasts')]
    public function testHydratesCompleteCapturedForecasts(
        string $fixture,
        float $latitude,
        float $longitude,
        array $airQualityIndexes,
    ): void {
        $forecast = Forecast::fromArray(Fixture::json($fixture));

        self::assertSame($latitude, $forecast->coordinates()?->latitude());
        self::assertSame($longitude, $forecast->coordinates()?->longitude());
        self::assertCount(96, $forecast->periods());
        self::assertContainsOnlyInstancesOf(Period::class, $forecast->periods());
        self::assertSame(1785614400, $forecast->periods()[0]->forecastAt()?->getTimestamp());
        self::assertSame(1785956400, $forecast->periods()[95]->forecastAt()?->getTimestamp());

        $actualAirQualityIndexes = array_values(array_unique(array_map(
            static fn (Period $period): ?int => $period->airQualityIndex()?->value,
            $forecast->periods(),
        )));
        sort($actualAirQualityIndexes);

        self::assertSame($airQualityIndexes, $actualAirQualityIndexes);
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Forecast::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertSame([], $missing->periods());

        $forecast = Forecast::fromArray([
            'coord' => [
                'lat' => null,
                'unknown' => new \stdClass(),
            ],
            'list' => [
                [],
                ['dt' => null, 'unknown' => new \stdClass()],
            ],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($forecast->coordinates()?->latitude());
        self::assertNull($forecast->coordinates()?->longitude());
        self::assertCount(2, $forecast->periods());
        self::assertNull($forecast->periods()[0]->forecastAt());
        self::assertNull($forecast->periods()[1]->airQualityIndex());

        self::assertSame([], Forecast::fromArray(['list' => null])->periods());
        self::assertNull(Forecast::fromArray(['coord' => null])->coordinates());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Forecast::fromArray($data);
    }

    public static function capturedForecasts(): iterable
    {
        yield 'good to moderate' => [
            'air-pollution/forecast/good-to-moderate.json',
            28.6139,
            77.209,
            [1, 2, 3],
        ];
        yield 'moderate to very poor' => [
            'air-pollution/forecast/moderate-to-very-poor.json',
            39.9042,
            116.4074,
            [3, 4, 5],
        ];
    }

    public static function invalidFields(): iterable
    {
        yield 'coordinates' => [
            ['coord' => 'invalid'],
            '"coord" expected array, string received.',
        ];
        yield 'latitude' => [
            ['coord' => ['lat' => '28.6']],
            '"lat" expected int|float, string received.',
        ];
        yield 'periods' => [
            ['list' => 'invalid'],
            '"list" expected array, string received.',
        ];
        yield 'period member' => [
            ['list' => ['invalid']],
            '"list.0" expected array, string received.',
        ];
        yield 'period field' => [
            ['list' => [['main' => 'invalid']]],
            '"main" expected array, string received.',
        ];
    }
}
