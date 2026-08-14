<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall\FifteenMinuteTimeline;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\FifteenMinuteTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedForecastPeriod(): void
    {
        $period = self::fromFixture('one-call/fifteen-minute/success.json', 7);

        self::assertSame(1785676500, $period->dateTime()?->getTimestamp());
        self::assertSame('UTC', $period->dateTime()?->getTimezone()->getName());
        self::assertSame(26.06, $period->temperature());
        self::assertSame(Unit::CELSIUS, $period->temperatureUnit());
        self::assertSame('26.06 °C', $period->temperatureWithUnit());
        self::assertSame(26.06, $period->feelsLikeTemperature());
        self::assertSame('26.06 °C', $period->feelsLikeTemperatureWithUnit());
        self::assertSame(1015.75, $period->pressure());
        self::assertSame(Unit::HECTOPASCAL, $period->pressureUnit());
        self::assertSame('1015.75 hPa', $period->pressureWithUnit());
        self::assertSame(55, $period->humidity());
        self::assertSame(Unit::PERCENT, $period->humidityUnit());
        self::assertSame('55 %', $period->humidityWithUnit());
        self::assertSame(16.45, $period->dewPoint());
        self::assertSame(Unit::CELSIUS, $period->dewPointUnit());
        self::assertSame('16.45 °C', $period->dewPointWithUnit());
        self::assertSame(8.06, $period->ultravioletIndex());
        self::assertSame(10000, $period->visibility());
        self::assertSame(Unit::METER, $period->visibilityUnit());
        self::assertSame('10000 m', $period->visibilityWithUnit());
        self::assertSame(5.21, $period->wind()?->speed());
        self::assertSame('5.21 m/s', $period->wind()?->speedWithUnit());
        self::assertSame(306, $period->wind()?->direction());
        self::assertNull($period->wind()?->gust());
        self::assertSame(67, $period->clouds()?->coverage());
        self::assertSame(0.0, $period->precipitationProbability());
        self::assertSame(Unit::PERCENT, $period->precipitationProbabilityUnit());
        self::assertSame('0 %', $period->precipitationProbabilityWithUnit());
        self::assertSame('Clouds', $period->conditions()[0]->group());
        self::assertNull($period->rain());
        self::assertNull($period->snow());
        self::assertSame([], $period->alertIds());
    }

    public function testHydratesCapturedRainConditionWithoutAmount(): void
    {
        $period = self::fromFixture('one-call/fifteen-minute/rain.json', 16);

        self::assertSame('Rain', $period->conditions()[0]->group());
        self::assertSame(91.0, $period->precipitationProbability());
        self::assertSame('91 %', $period->precipitationProbabilityWithUnit());
        self::assertNull($period->rain());
        self::assertNull($period->snow());
    }

    public function testHydratesCapturedSnowConditionAndAlertIdsWithoutAmount(): void
    {
        $period = self::fromFixture('one-call/fifteen-minute/snow-alerts.json');

        self::assertSame('Snow', $period->conditions()[0]->group());
        self::assertSame(100.0, $period->precipitationProbability());
        self::assertNull($period->rain());
        self::assertNull($period->snow());
        self::assertSame([
            'urn:oid:2.49.0.0.152.0.2026.7.31.14.20.43:f1076d7511a15522d5a6e41917020bc0',
        ], $period->alertIds());
    }

    public function testHydratesDocumentedConditionalPrecipitation(): void
    {
        $period = Period::fromArray([
            'rain' => ['1h' => 1.25],
            'snow' => ['1h' => 0.5],
        ]);

        self::assertSame(1.25, $period->rain()?->lastHour());
        self::assertSame(Unit::MILLIMETERS_PER_HOUR, $period->rain()?->lastHourUnit());
        self::assertSame('1.25 mm/h', $period->rain()?->lastHourWithUnit());
        self::assertSame(0.5, $period->snow()?->lastHour());
        self::assertSame('0.5 mm/h', $period->snow()?->lastHourWithUnit());
    }

    public function testRetainsUnitsFromHydrationContext(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $period = Period::fromArray([
            'temp' => 72.5,
            'feels_like' => 71,
            'dew_point' => 60,
            'wind_speed' => 10,
            'wind_gust' => 15,
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $period->temperatureUnit());
        self::assertSame('72.5 °F', $period->temperatureWithUnit());
        self::assertSame('71 °F', $period->feelsLikeTemperatureWithUnit());
        self::assertSame('60 °F', $period->dewPointWithUnit());
        self::assertSame(Unit::MILES_PER_HOUR, $period->wind()?->speedUnit());
        self::assertSame('10 mph', $period->wind()?->speedWithUnit());
        self::assertSame('15 mph', $period->wind()?->gustWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Period::fromArray([]);

        self::assertNull($missing->dateTime());
        self::assertNull($missing->temperature());
        self::assertNull($missing->temperatureWithUnit());
        self::assertNull($missing->pressure());
        self::assertNull($missing->wind());
        self::assertNull($missing->clouds());
        self::assertSame([], $missing->conditions());
        self::assertNull($missing->rain());
        self::assertNull($missing->snow());
        self::assertSame([], $missing->alertIds());

        $period = Period::fromArray([
            'dt' => null,
            'temp' => null,
            'weather' => [['icon' => null, 'unknown' => true]],
            'clouds' => null,
            'wind_speed' => null,
            'rain' => ['1h' => null, 'unknown' => true],
            'snow' => null,
            'alerts' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($period->dateTime());
        self::assertNull($period->temperature());
        self::assertCount(1, $period->conditions());
        self::assertNull($period->conditions()[0]->icon());
        self::assertNull($period->clouds()?->coverage());
        self::assertNull($period->wind()?->speed());
        self::assertNull($period->rain()?->lastHour());
        self::assertNull($period->snow());
        self::assertSame([], $period->alertIds());
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
            ['dt' => '1785676500'],
            '"dt" expected int, string received.',
        ];
        yield 'temperature' => [
            ['temp' => '26.06'],
            '"temp" expected int|float, string received.',
        ];
        yield 'pressure' => [
            ['pressure' => '1015.75'],
            '"pressure" expected int|float, string received.',
        ];
        yield 'humidity' => [
            ['humidity' => 55.5],
            '"humidity" expected int, float received.',
        ];
        yield 'visibility' => [
            ['visibility' => 10000.5],
            '"visibility" expected int, float received.',
        ];
        yield 'wind speed' => [
            ['wind_speed' => '5.21'],
            '"wind_speed" expected int|float, string received.',
        ];
        yield 'cloud coverage' => [
            ['clouds' => 67.5],
            '"clouds" expected int, float received.',
        ];
        yield 'precipitation probability' => [
            ['pop' => '0.5'],
            '"pop" expected int|float, string received.',
        ];
        yield 'conditions' => [
            ['weather' => 'Clouds'],
            '"weather" expected array, string received.',
        ];
        yield 'condition member' => [
            ['weather' => ['Clouds']],
            '"weather.0" expected array, string received.',
        ];
        yield 'rain' => [
            ['rain' => 'invalid'],
            '"rain" expected array, string received.',
        ];
        yield 'rain amount' => [
            ['rain' => ['1h' => '1.25']],
            '"1h" expected int|float, string received.',
        ];
        yield 'alert IDs' => [
            ['alerts' => 'invalid'],
            '"alerts" expected array, string received.',
        ];
        yield 'alert ID member' => [
            ['alerts' => [123]],
            '"alerts.0" expected string, int received.',
        ];
    }

    private static function fromFixture(string $path, int $index = 0): Period
    {
        $response = Fixture::json($path);

        return Period::fromArray($response['data'][$index]);
    }
}
