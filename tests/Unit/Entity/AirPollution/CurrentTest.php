<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\AirPollution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Current;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class CurrentTest extends TestCase
{
    public function testHydratesCapturedCurrentAirPollution(): void
    {
        $current = Current::fromArray(
            Fixture::json('air-pollution/current/good.json'),
        );

        self::assertSame(-33.8679, $current->coordinates()?->latitude());
        self::assertSame(151.2073, $current->coordinates()?->longitude());
        self::assertSame(1785616883, $current->dateTime()?->getTimestamp());
        self::assertSame('UTC', $current->dateTime()?->getTimezone()->getName());
        self::assertSame(AirQualityIndex::GOOD, $current->airQualityIndex());

        self::assertSame(96.56, $current->components()?->carbonMonoxide());
        self::assertSame('96.56 µg/m³', $current->components()?->carbonMonoxideWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Current::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertNull($missing->dateTime());
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

        self::assertNull($current->coordinates()?->latitude());
        self::assertNull($current->coordinates()?->longitude());
        self::assertNull($current->dateTime());
        self::assertNull($current->airQualityIndex());
        self::assertNull($current->components()?->carbonMonoxide());
        self::assertNull($current->components()?->nitrogenMonoxide());

        self::assertNull(Current::fromArray(['list' => null])->dateTime());
        self::assertNull(Current::fromArray(['list' => []])->dateTime());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(array $data, string $message): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage($message);

        Current::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'coordinates' => [
            ['coord' => 'invalid'],
            '"coord" expected array, string received.',
        ];
        yield 'latitude' => [
            ['coord' => ['lat' => '-33.8']],
            '"lat" expected int|float, string received.',
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
    }
}
