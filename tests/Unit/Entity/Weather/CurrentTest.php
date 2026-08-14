<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Weather;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Current;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class CurrentTest extends TestCase
{
    public function testHydratesCapturedCurrentWeather(): void
    {
        $weather = Current::fromArray(
            Fixture::json('weather/current/success.json'),
        );

        self::assertSame(38.7223, $weather->coordinates()?->latitude());
        self::assertSame(-9.1393, $weather->coordinates()?->longitude());
        self::assertSame(22.55, $weather->temperature());
        self::assertSame(Unit::CELSIUS, $weather->temperatureUnit());
        self::assertSame('22.55 °C', $weather->temperatureWithUnit());
        self::assertSame(22.87, $weather->feelsLikeTemperature());
        self::assertSame('22.87 °C', $weather->feelsLikeTemperatureWithUnit());
        self::assertSame(21.48, $weather->minimumTemperature());
        self::assertSame('21.48 °C', $weather->minimumTemperatureWithUnit());
        self::assertSame(23.94, $weather->maximumTemperature());
        self::assertSame('23.94 °C', $weather->maximumTemperatureWithUnit());
        self::assertSame(1016, $weather->pressure());
        self::assertSame('1016 hPa', $weather->pressureWithUnit());
        self::assertSame(77, $weather->humidity());
        self::assertSame('77 %', $weather->humidityWithUnit());
        self::assertSame(1016, $weather->seaLevelPressure());
        self::assertSame('1016 hPa', $weather->seaLevelPressureWithUnit());
        self::assertSame(1006, $weather->groundLevelPressure());
        self::assertSame('1006 hPa', $weather->groundLevelPressureWithUnit());
        self::assertSame(10000, $weather->visibility());
        self::assertSame('10000 m', $weather->visibilityWithUnit());

        self::assertCount(1, $weather->conditions());
        self::assertSame(802, $weather->conditions()[0]->id());
        self::assertSame('Clouds', $weather->conditions()[0]->group());
        self::assertSame('scattered clouds', $weather->conditions()[0]->description());
        self::assertSame('03d', $weather->conditions()[0]->icon());
        self::assertSame(
            'https://openweathermap.org/img/wn/03d@2x.png',
            $weather->conditions()[0]->iconUrl(),
        );

        self::assertSame(4.47, $weather->wind()?->speed());
        self::assertSame(Unit::METERS_PER_SECOND, $weather->wind()?->speedUnit());
        self::assertSame('4.47 m/s', $weather->wind()?->speedWithUnit());
        self::assertSame(190, $weather->wind()?->direction());
        self::assertSame('190 °', $weather->wind()?->directionWithUnit());
        self::assertSame(7.6, $weather->wind()?->gust());
        self::assertSame('7.6 m/s', $weather->wind()?->gustWithUnit());

        self::assertSame(48, $weather->clouds()?->coverage());
        self::assertSame(Unit::PERCENT, $weather->clouds()?->coverageUnit());
        self::assertSame('48 %', $weather->clouds()?->coverageWithUnit());
        self::assertNull($weather->rain());
        self::assertNull($weather->snow());

        self::assertSame(1785573885, $weather->dateTime()?->getTimestamp());
        self::assertSame('UTC', $weather->dateTime()?->getTimezone()->getName());
        self::assertSame('PT', $weather->countryCode());
        self::assertSame(1785562660, $weather->sunriseAt()?->getTimestamp());
        self::assertSame(1785613679, $weather->sunsetAt()?->getTimestamp());
        self::assertSame(3600, $weather->timezoneOffset());
        self::assertSame(8012502, $weather->id());
        self::assertSame('Socorro', $weather->name());
    }

    public function testHydratesConditionalRain(): void
    {
        $weather = Current::fromArray(
            Fixture::json('weather/current/rain.json'),
        );

        self::assertSame('Rain', $weather->conditions()[0]->group());
        self::assertSame(2.47, $weather->rain()?->lastHour());
        self::assertSame(Unit::MILLIMETERS_PER_HOUR, $weather->rain()?->lastHourUnit());
        self::assertSame('2.47 mm/h', $weather->rain()?->lastHourWithUnit());
        self::assertNull($weather->snow());
    }

    public function testHydratesConditionalSnowAndMissingFields(): void
    {
        $weather = Current::fromArray(
            Fixture::json('weather/current/snow.json'),
        );

        self::assertSame('Snow', $weather->conditions()[0]->group());
        self::assertSame(1.37, $weather->snow()?->lastHour());
        self::assertSame('1.37 mm/h', $weather->snow()?->lastHourWithUnit());
        self::assertNull($weather->rain());
        self::assertNull($weather->visibility());
        self::assertNull($weather->visibilityWithUnit());
    }

    public function testRetainsUnitsFromHydrationContext(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $weather = Current::fromArray([
            'main' => ['temp' => 72.5],
            'wind' => ['speed' => 10, 'gust' => 15],
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $weather->temperatureUnit());
        self::assertSame('72.5 °F', $weather->temperatureWithUnit());
        self::assertSame(Unit::MILES_PER_HOUR, $weather->wind()?->speedUnit());
        self::assertSame('10 mph', $weather->wind()?->speedWithUnit());
        self::assertSame('15 mph', $weather->wind()?->gustWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        self::assertNull(Current::fromArray([])->coordinates());
        self::assertSame([], Current::fromArray(['weather' => null])->conditions());

        $weather = Current::fromArray([
            'coord' => ['lat' => null, 'unknown' => true],
            'weather' => [['icon' => null]],
            'main' => ['temp' => null],
            'base' => new \stdClass(),
            'wind' => null,
            'clouds' => ['all' => null],
            'rain' => ['1h' => null, 'unknown' => true],
            'sys' => [
                'type' => new \stdClass(),
                'id' => new \stdClass(),
            ],
            'cod' => new \stdClass(),
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($weather->coordinates()?->latitude());
        self::assertNull($weather->coordinates()?->longitude());
        self::assertCount(1, $weather->conditions());
        self::assertNull($weather->conditions()[0]->icon());
        self::assertNull($weather->conditions()[0]->iconUrl());
        self::assertNull($weather->temperature());
        self::assertSame(Unit::CELSIUS, $weather->temperatureUnit());
        self::assertNull($weather->temperatureWithUnit());
        self::assertNull($weather->feelsLikeTemperature());
        self::assertNull($weather->pressure());
        self::assertNull($weather->wind());
        self::assertNull($weather->clouds()?->coverage());
        self::assertNull($weather->clouds()?->coverageWithUnit());
        self::assertNull($weather->rain()?->lastHour());
        self::assertNull($weather->rain()?->lastHourWithUnit());
        self::assertNull($weather->snow());
        self::assertNull($weather->dateTime());
        self::assertNull($weather->countryCode());
        self::assertNull($weather->sunriseAt());
        self::assertNull($weather->name());
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

        Current::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'coordinates' => [['coord' => 'invalid'], 'coord', 'array', 'string'];
        yield 'latitude' => [['coord' => ['lat' => '38.7']], 'lat', 'int|float', 'string'];
        yield 'conditions' => [['weather' => 'Clouds'], 'weather', 'array', 'string'];
        yield 'condition member' => [['weather' => ['Clouds']], 'weather.0', 'array', 'string'];
        yield 'condition id' => [['weather' => [['id' => '802']]], 'id', 'int', 'string'];
        yield 'measurements' => [['main' => 'invalid'], 'main', 'array', 'string'];
        yield 'temperature' => [['main' => ['temp' => '22.5']], 'main.temp', 'int|float', 'string'];
        yield 'wind speed' => [['wind' => ['speed' => '4.5']], 'speed', 'int|float', 'string'];
        yield 'cloud coverage' => [['clouds' => ['all' => 48.5]], 'all', 'int', 'float'];
        yield 'rain' => [['rain' => ['1h' => '2.5']], '1h', 'int|float', 'string'];
        yield 'observation time' => [['dt' => '1785573885'], 'dt', 'int', 'string'];
        yield 'country' => [['sys' => ['country' => 1]], 'sys.country', 'string', 'int'];
    }
}
