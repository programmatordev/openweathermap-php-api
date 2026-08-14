<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall\OneHourTimeline;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneHourTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedPeriod(): void
    {
        $period = self::fromFixture('one-call/one-hour/success.json');

        self::assertSame(1785668400, $period->dateTime()?->getTimestamp());
        self::assertSame('UTC', $period->dateTime()?->getTimezone()->getName());
        self::assertSame(25.05, $period->temperature());
        self::assertSame(Unit::CELSIUS, $period->temperatureUnit());
        self::assertSame('25.05 °C', $period->temperatureWithUnit());
        self::assertSame(25.23, $period->feelsLikeTemperature());
        self::assertSame(1015.0, $period->pressure());
        self::assertSame('1015 hPa', $period->pressureWithUnit());
        self::assertSame(62, $period->humidity());
        self::assertSame(17.27, $period->dewPoint());
        self::assertSame(7.53, $period->ultravioletIndex());
        self::assertSame(10000, $period->visibility());
        self::assertSame(3.87, $period->wind()?->speed());
        self::assertSame(317, $period->wind()?->direction());
        self::assertSame(4.47, $period->wind()?->gust());
        self::assertSame(48, $period->clouds()?->coverage());
        self::assertSame(0.0, $period->precipitationProbability());
        self::assertSame('Clouds', $period->conditions()[0]->group());
        self::assertNull($period->rain());
        self::assertNull($period->snow());
        self::assertSame([], $period->alertIds());
    }

    public function testHydratesCapturedRain(): void
    {
        $period = self::fromFixture('one-call/one-hour/rain.json');

        self::assertSame('Rain', $period->conditions()[0]->group());
        self::assertSame(1.0, $period->precipitationProbability());
        self::assertSame(1.72, $period->rain()?->lastHour());
        self::assertSame(Unit::MILLIMETERS_PER_HOUR, $period->rain()?->lastHourUnit());
        self::assertSame('1.72 mm/h', $period->rain()?->lastHourWithUnit());
        self::assertNull($period->snow());
    }

    public function testHydratesCapturedSnowAndAlerts(): void
    {
        $period = self::fromFixture('one-call/one-hour/snow-alerts.json');
        $periodWithoutVisibility = self::fromFixture(
            'one-call/one-hour/snow-alerts.json',
            19,
        );

        self::assertSame('Snow', $period->conditions()[0]->group());
        self::assertSame(1.0, $period->precipitationProbability());
        self::assertSame(2.54, $period->snow()?->lastHour());
        self::assertSame('2.54 mm/h', $period->snow()?->lastHourWithUnit());
        self::assertNull($period->rain());
        self::assertSame([
            'urn:oid:2.49.0.0.152.0.2026.7.31.14.20.43:f1076d7511a15522d5a6e41917020bc0',
        ], $period->alertIds());
        self::assertNull($periodWithoutVisibility->visibility());
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
        yield 'date time' => [
            ['dt' => '1785668400'],
            '"dt" expected int, string received.',
        ];
        yield 'temperature' => [
            ['temp' => '25.05'],
            '"temp" expected int|float, string received.',
        ];
        yield 'rain amount' => [
            ['rain' => ['1h' => '1.72']],
            '"1h" expected int|float, string received.',
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
