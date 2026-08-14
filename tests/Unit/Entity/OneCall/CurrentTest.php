<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Current;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class CurrentTest extends TestCase
{
    public function testHydratesCapturedCurrentWeather(): void
    {
        $current = Current::fromArray(
            Fixture::json('one-call/current/success.json'),
        );

        self::assertSame(38.7223, $current->coordinates()?->latitude());
        self::assertSame(-9.1393, $current->coordinates()?->longitude());
        self::assertSame('Europe/Lisbon', $current->timezone()?->identifier());
        self::assertSame(3600, $current->timezone()?->offsetSeconds());
        self::assertSame(1785668004, $current->dateTime()?->getTimestamp());
        self::assertSame('UTC', $current->dateTime()?->getTimezone()->getName());
        self::assertSame(1785649113, $current->sunriseAt()?->getTimestamp());
        self::assertSame(1785700020, $current->sunsetAt()?->getTimestamp());

        self::assertSame(24.34, $current->temperature());
        self::assertSame(Unit::CELSIUS, $current->temperatureUnit());
        self::assertSame('24.34 °C', $current->temperatureWithUnit());
        self::assertSame(24.68, $current->feelsLikeTemperature());
        self::assertSame('24.68 °C', $current->feelsLikeTemperatureWithUnit());
        self::assertSame(1016, $current->pressure());
        self::assertSame(Unit::HECTOPASCAL, $current->pressureUnit());
        self::assertSame('1016 hPa', $current->pressureWithUnit());
        self::assertSame(71, $current->humidity());
        self::assertSame(Unit::PERCENT, $current->humidityUnit());
        self::assertSame('71 %', $current->humidityWithUnit());
        self::assertSame(18.75, $current->dewPoint());
        self::assertSame(Unit::CELSIUS, $current->dewPointUnit());
        self::assertSame('18.75 °C', $current->dewPointWithUnit());
        self::assertSame(7.53, $current->ultravioletIndex());
        self::assertSame(40, $current->clouds()?->coverage());
        self::assertSame(Unit::PERCENT, $current->clouds()?->coverageUnit());
        self::assertSame('40 %', $current->clouds()?->coverageWithUnit());
        self::assertSame(10000, $current->visibility());
        self::assertSame(Unit::METER, $current->visibilityUnit());
        self::assertSame('10000 m', $current->visibilityWithUnit());
        self::assertSame(2.24, $current->wind()?->speed());
        self::assertSame(Unit::METERS_PER_SECOND, $current->wind()?->speedUnit());
        self::assertSame('2.24 m/s', $current->wind()?->speedWithUnit());
        self::assertSame(293, $current->wind()?->direction());
        self::assertSame(Unit::DEGREE, $current->wind()?->directionUnit());
        self::assertSame('293 °', $current->wind()?->directionWithUnit());
        self::assertSame(5.36, $current->wind()?->gust());
        self::assertSame(Unit::METERS_PER_SECOND, $current->wind()?->gustUnit());
        self::assertSame('5.36 m/s', $current->wind()?->gustWithUnit());

        self::assertCount(1, $current->conditions());
        self::assertSame(802, $current->conditions()[0]->id());
        self::assertSame('Clouds', $current->conditions()[0]->group());
        self::assertSame('scattered clouds', $current->conditions()[0]->description());
        self::assertSame('03d', $current->conditions()[0]->icon());
        self::assertSame(
            'https://openweathermap.org/img/wn/03d@2x.png',
            $current->conditions()[0]->iconUrl(),
        );
        self::assertNull($current->rain());
        self::assertNull($current->snow());
        self::assertSame([], $current->alertIds());
    }

    public function testHydratesConditionalRain(): void
    {
        $current = Current::fromArray(
            Fixture::json('one-call/current/rain.json'),
        );

        self::assertSame('Rain', $current->conditions()[0]->group());
        self::assertSame(0.13, $current->rain()?->lastHour());
        self::assertSame(Unit::MILLIMETERS_PER_HOUR, $current->rain()?->lastHourUnit());
        self::assertSame('0.13 mm/h', $current->rain()?->lastHourWithUnit());
        self::assertNull($current->snow());
        self::assertSame([], $current->alertIds());
    }

    public function testHydratesConditionalSnowAndAlertIds(): void
    {
        $current = Current::fromArray(
            Fixture::json('one-call/current/snow.json'),
        );

        self::assertSame('Snow', $current->conditions()[0]->group());
        self::assertSame(4.86, $current->snow()?->lastHour());
        self::assertSame('4.86 mm/h', $current->snow()?->lastHourWithUnit());
        self::assertNull($current->rain());
        self::assertSame([
            'urn:oid:2.49.0.0.152.0.2026.7.31.14.20.43:f1076d7511a15522d5a6e41917020bc0',
        ], $current->alertIds());
    }

    public function testRetainsUnitsFromHydrationContext(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $current = Current::fromArray([
            'data' => [[
                'temp' => 72.5,
                'feels_like' => 71,
                'dew_point' => 60,
                'wind_speed' => 10,
                'wind_gust' => 15,
            ]],
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $current->temperatureUnit());
        self::assertSame('72.5 °F', $current->temperatureWithUnit());
        self::assertSame('71 °F', $current->feelsLikeTemperatureWithUnit());
        self::assertSame('60 °F', $current->dewPointWithUnit());
        self::assertSame(Unit::MILES_PER_HOUR, $current->wind()?->speedUnit());
        self::assertSame('10 mph', $current->wind()?->speedWithUnit());
        self::assertSame('15 mph', $current->wind()?->gustWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Current::fromArray([]);

        self::assertNull($missing->coordinates());
        self::assertNull($missing->timezone());
        self::assertNull($missing->dateTime());
        self::assertNull($missing->temperature());
        self::assertNull($missing->temperatureWithUnit());
        self::assertNull($missing->dewPoint());
        self::assertNull($missing->ultravioletIndex());
        self::assertNull($missing->wind());
        self::assertNull($missing->clouds());
        self::assertSame([], $missing->conditions());
        self::assertNull($missing->rain());
        self::assertNull($missing->snow());
        self::assertSame([], $missing->alertIds());

        $current = Current::fromArray([
            'lat' => null,
            'timezone' => null,
            'data' => [[
                'dt' => null,
                'temp' => null,
                'weather' => [['icon' => null, 'unknown' => true]],
                'rain' => ['1h' => null, 'unknown' => true],
                'snow' => null,
                'alerts' => null,
                'unknown' => new \stdClass(),
            ]],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($current->coordinates()?->latitude());
        self::assertNull($current->coordinates()?->longitude());
        self::assertNull($current->timezone()?->identifier());
        self::assertNull($current->timezone()?->offsetSeconds());
        self::assertCount(1, $current->conditions());
        self::assertNull($current->conditions()[0]->icon());
        self::assertNull($current->temperature());
        self::assertNull($current->wind());
        self::assertNull($current->clouds());
        self::assertNull($current->rain()?->lastHour());
        self::assertNull($current->snow());
        self::assertSame([], $current->alertIds());

        self::assertNull(Current::fromArray(['data' => null])->dateTime());
        self::assertNull(Current::fromArray(['data' => []])->dateTime());
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
        yield 'latitude' => [
            ['lat' => '38.7'],
            '"lat" expected int|float, string received.',
        ];
        yield 'timezone' => [
            ['timezone' => 1],
            '"timezone" expected string, int received.',
        ];
        yield 'data' => [
            ['data' => 'invalid'],
            '"data" expected array, string received.',
        ];
        yield 'data member' => [
            ['data' => ['invalid']],
            '"data.0" expected array, string received.',
        ];
        yield 'observation time' => [
            ['data' => [['dt' => '1785668004']]],
            '"data.0.dt" expected int, string received.',
        ];
        yield 'temperature' => [
            ['data' => [['temp' => '24.34']]],
            '"data.0.temp" expected int|float, string received.',
        ];
        yield 'pressure' => [
            ['data' => [['pressure' => 1016.5]]],
            '"data.0.pressure" expected int, float received.',
        ];
        yield 'wind speed' => [
            ['data' => [['wind_speed' => '2.24']]],
            '"data.0.wind_speed" expected int|float, string received.',
        ];
        yield 'cloud coverage' => [
            ['data' => [['clouds' => 40.5]]],
            '"data.0.clouds" expected int, float received.',
        ];
        yield 'conditions' => [
            ['data' => [['weather' => 'Clouds']]],
            '"data.0.weather" expected array, string received.',
        ];
        yield 'condition member' => [
            ['data' => [['weather' => ['Clouds']]]],
            '"data.0.weather.0" expected array, string received.',
        ];
        yield 'rain' => [
            ['data' => [['rain' => 'invalid']]],
            '"data.0.rain" expected array, string received.',
        ];
        yield 'alert IDs' => [
            ['data' => [['alerts' => 'invalid']]],
            '"data.0.alerts" expected array, string received.',
        ];
        yield 'alert ID member' => [
            ['data' => [['alerts' => [123]]]],
            '"data.0.alerts.0" expected string, int received.',
        ];
    }
}
