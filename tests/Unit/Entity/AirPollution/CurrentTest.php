<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Current;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class CurrentTest extends TestCase
{
    public function testHydratesCapturedCurrentAirPollution(): void
    {
        $current = Current::fromArray(
            Fixture::json('air-pollution/current/good.json'),
        );

        self::assertSame(-33.8679, $current->latitude());
        self::assertSame(151.2073, $current->longitude());
        self::assertSame(1785616883, $current->observedAt()?->getTimestamp());
        self::assertSame('UTC', $current->observedAt()?->getTimezone()->getName());
        self::assertSame(AirQualityIndex::GOOD, $current->airQualityIndex());

        $components = $current->components();

        self::assertSame(96.56, $components?->carbonMonoxide());
        self::assertSame('96.56 µg/m³', $components?->carbonMonoxideWithUnit());
        self::assertSame(0.01, $components?->nitrogenMonoxide());
        self::assertSame('0.01 µg/m³', $components?->nitrogenMonoxideWithUnit());
        self::assertSame(7.17, $components?->nitrogenDioxide());
        self::assertSame('7.17 µg/m³', $components?->nitrogenDioxideWithUnit());
        self::assertSame(29.69, $components?->ozone());
        self::assertSame('29.69 µg/m³', $components?->ozoneWithUnit());
        self::assertSame(1.03, $components?->sulphurDioxide());
        self::assertSame('1.03 µg/m³', $components?->sulphurDioxideWithUnit());
        self::assertSame(5.89, $components?->fineParticulateMatter());
        self::assertSame('5.89 µg/m³', $components?->fineParticulateMatterWithUnit());
        self::assertSame(7.62, $components?->coarseParticulateMatter());
        self::assertSame('7.62 µg/m³', $components?->coarseParticulateMatterWithUnit());
        self::assertSame(0.65, $components?->ammonia());
        self::assertSame('0.65 µg/m³', $components?->ammoniaWithUnit());
        self::assertSame(
            Unit::MICROGRAMS_PER_CUBIC_METER,
            $components?->fineParticulateMatterUnit(),
        );
    }

    #[DataProvider('airQualityIndexes')]
    public function testHydratesEveryDocumentedAirQualityIndex(
        int $value,
        AirQualityIndex $expected,
    ): void {
        $current = Current::fromArray([
            'list' => [['main' => ['aqi' => $value]]],
        ]);

        self::assertSame($expected, $current->airQualityIndex());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Current::fromArray([]);

        self::assertNull($missing->latitude());
        self::assertNull($missing->longitude());
        self::assertNull($missing->observedAt());
        self::assertNull($missing->airQualityIndex());
        self::assertNull($missing->components());

        $current = Current::fromArray([
            'coord' => ['lat' => null, 'unknown' => true],
            'list' => [[
                'dt' => null,
                'main' => ['aqi' => null, 'unknown' => true],
                'components' => [
                    'co' => null,
                    'unknown' => new \stdClass(),
                ],
                'unknown' => new \stdClass(),
            ]],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($current->latitude());
        self::assertNull($current->longitude());
        self::assertNull($current->observedAt());
        self::assertNull($current->airQualityIndex());
        self::assertNull($current->components()?->carbonMonoxide());
        self::assertNull($current->components()?->nitrogenMonoxide());
        self::assertSame(
            Unit::MICROGRAMS_PER_CUBIC_METER,
            $current->components()?->nitrogenMonoxideUnit(),
        );

        self::assertNull(Current::fromArray(['list' => null])->observedAt());
        self::assertNull(Current::fromArray(['list' => []])->observedAt());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Current::fromArray($data);
    }

    public static function airQualityIndexes(): iterable
    {
        yield 'good' => [1, AirQualityIndex::GOOD];
        yield 'fair' => [2, AirQualityIndex::FAIR];
        yield 'moderate' => [3, AirQualityIndex::MODERATE];
        yield 'poor' => [4, AirQualityIndex::POOR];
        yield 'very poor' => [5, AirQualityIndex::VERY_POOR];
    }

    public static function invalidFields(): iterable
    {
        yield 'coordinates' => [
            ['coord' => 'invalid'],
            '"coord" expected array, string received.',
        ];
        yield 'latitude' => [
            ['coord' => ['lat' => '-33.8']],
            '"coord.lat" expected int|float, string received.',
        ];
        yield 'list' => [
            ['list' => 'invalid'],
            '"list" expected array, string received.',
        ];
        yield 'list member' => [
            ['list' => ['invalid']],
            '"list.0" expected array, string received.',
        ];
        yield 'date and time' => [
            ['list' => [['dt' => '1785616883']]],
            '"list.0.dt" expected int, string received.',
        ];
        yield 'main' => [
            ['list' => [['main' => 'invalid']]],
            '"list.0.main" expected array, string received.',
        ];
        yield 'AQI type' => [
            ['list' => [['main' => ['aqi' => '1']]]],
            '"list.0.main.aqi" expected int, string received.',
        ];
        yield 'AQI float' => [
            ['list' => [['main' => ['aqi' => 1.0]]]],
            '"list.0.main.aqi" expected int, float received.',
        ];
        yield 'unsupported AQI' => [
            ['list' => [['main' => ['aqi' => 6]]]],
            '"list.0.main.aqi" expected an integer from 1 through 5, "6" received.',
        ];
        yield 'components' => [
            ['list' => [['components' => 'invalid']]],
            '"list.0.components" expected array, string received.',
        ];
        yield 'component concentration' => [
            ['list' => [['components' => ['co' => '96.56']]]],
            '"co" expected int|float, string received.',
        ];
    }
}
