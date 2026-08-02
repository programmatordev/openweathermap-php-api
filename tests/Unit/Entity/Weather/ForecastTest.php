<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Weather;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class ForecastTest extends TestCase
{
    public function testHydratesCapturedForecast(): void
    {
        $forecast = Forecast::fromArray(
            Fixture::json('weather/forecast/success.json'),
        );

        self::assertSame(40, $forecast->count());
        self::assertCount(40, $forecast->periods());
        self::assertSame(1785574800, $forecast->periods()[0]->forecastAt()?->getTimestamp());
        self::assertSame(22.54, $forecast->periods()[0]->temperature());

        $city = $forecast->city();

        self::assertSame(6458923, $city?->id());
        self::assertSame('Lisbon Municipality', $city?->name());
        self::assertSame(38.7223, $city?->coordinates()?->latitude());
        self::assertSame(-9.1393, $city?->coordinates()?->longitude());
        self::assertSame('PT', $city?->countryCode());
        self::assertSame(0, $city?->population());
        self::assertSame(3600, $city?->timezoneOffset());
        self::assertSame(1785562660, $city?->sunriseAt()?->getTimestamp());
        self::assertSame('UTC', $city?->sunriseAt()?->getTimezone()->getName());
        self::assertSame(1785613679, $city?->sunsetAt()?->getTimestamp());
        self::assertSame('UTC', $city?->sunsetAt()?->getTimezone()->getName());
    }

    public function testPropagatesHydrationContextToPeriods(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $forecast = Forecast::fromArray([
            'list' => [
                ['main' => ['temp' => 72.5]],
            ],
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $forecast->periods()[0]->temperatureUnit());
        self::assertSame('72.5 °F', $forecast->periods()[0]->temperatureWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        self::assertNull(Forecast::fromArray(['city' => null])->city());
        self::assertNull(Forecast::fromArray(['city' => []])->city()?->coordinates());

        $forecast = Forecast::fromArray([
            'cod' => new \stdClass(),
            'message' => new \stdClass(),
            'cnt' => null,
            'list' => null,
            'city' => [
                'id' => null,
                'coord' => [
                    'lat' => null,
                    'unknown' => true,
                ],
                'sunrise' => null,
                'unknown' => new \stdClass(),
            ],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($forecast->count());
        self::assertSame([], $forecast->periods());
        self::assertNull($forecast->city()?->id());
        self::assertNull($forecast->city()?->name());
        self::assertNull($forecast->city()?->coordinates()?->latitude());
        self::assertNull($forecast->city()?->coordinates()?->longitude());
        self::assertNull($forecast->city()?->countryCode());
        self::assertNull($forecast->city()?->population());
        self::assertNull($forecast->city()?->timezoneOffset());
        self::assertNull($forecast->city()?->sunriseAt());
        self::assertNull($forecast->city()?->sunsetAt());
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFieldTypes(
        array $data,
        string $path,
        string $expectedType,
        string $receivedType,
    ): void {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(sprintf(
            '"%s" expected %s, %s received.',
            $path,
            $expectedType,
            $receivedType,
        ));

        Forecast::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'count' => [['cnt' => 40.0], 'cnt', 'int', 'float'];
        yield 'periods' => [['list' => 'invalid'], 'list', 'array', 'string'];
        yield 'period member' => [['list' => ['invalid']], 'list.0', 'array', 'string'];
        yield 'city' => [['city' => 'invalid'], 'city', 'array', 'string'];
        yield 'city id' => [['city' => ['id' => '1']], 'id', 'int', 'string'];
        yield 'city name' => [['city' => ['name' => 1]], 'name', 'string', 'int'];
        yield 'coordinates' => [['city' => ['coord' => 'invalid']], 'coord', 'array', 'string'];
        yield 'latitude' => [['city' => ['coord' => ['lat' => '38.7']]], 'lat', 'int|float', 'string'];
        yield 'country' => [['city' => ['country' => 1]], 'country', 'string', 'int'];
        yield 'population' => [['city' => ['population' => 1.5]], 'population', 'int', 'float'];
        yield 'timezone' => [['city' => ['timezone' => '3600']], 'timezone', 'int', 'string'];
        yield 'sunrise' => [['city' => ['sunrise' => '1785562660']], 'sunrise', 'int', 'string'];
        yield 'sunset' => [['city' => ['sunset' => 1785613679.5]], 'sunset', 'int', 'float'];
    }
}
