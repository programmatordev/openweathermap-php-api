<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\OneCall\OneDayTimeline;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedDailyPeriod(): void
    {
        $period = self::fromFixture('one-call/one-day/success.json');

        self::assertSame(1785628800, $period->dateTime()?->getTimestamp());
        self::assertSame('UTC', $period->dateTime()?->getTimezone()->getName());
        self::assertSame(1785649113, $period->sunriseAt()?->getTimestamp());
        self::assertSame(1785700020, $period->sunsetAt()?->getTimestamp());
        self::assertSame(1785706800, $period->moonriseAt()?->getTimestamp());
        self::assertSame(1785662640, $period->moonsetAt()?->getTimestamp());
        self::assertSame(0.62, $period->moonPhase());

        self::assertSame(25.53, $period->temperature()?->day());
        self::assertSame(18.73, $period->temperature()?->minimum());
        self::assertSame(26.99, $period->temperature()?->maximum());
        self::assertSame(19.4, $period->temperature()?->night());
        self::assertSame(24.82, $period->temperature()?->evening());
        self::assertSame(18.74, $period->temperature()?->morning());
        self::assertSame(25.53, $period->feelsLikeTemperature()?->day());
        self::assertSame(19.4, $period->feelsLikeTemperature()?->night());

        self::assertSame(1015.44, $period->pressure());
        self::assertSame(Unit::HECTOPASCAL, $period->pressureUnit());
        self::assertSame('1015.44 hPa', $period->pressureWithUnit());
        self::assertSame(48, $period->humidity());
        self::assertNull($period->dewPoint());
        self::assertSame(0.0, $period->ultravioletIndex());
        self::assertNull($period->visibility());
        self::assertSame(6.17, $period->wind()?->speed());
        self::assertSame(307, $period->wind()?->direction());
        self::assertNull($period->wind()?->gust());
        self::assertSame(41, $period->clouds()?->coverage());
        self::assertSame(0.0, $period->precipitationProbability());
        self::assertSame(Unit::PERCENT, $period->precipitationProbabilityUnit());
        self::assertSame('0 %', $period->precipitationProbabilityWithUnit());
        self::assertSame('Clouds', $period->conditions()[0]->group());
        self::assertNull($period->rain());
        self::assertNull($period->snow());
        self::assertSame([], $period->alertIds());
    }

    public function testHydratesCapturedScalarRainAndSnow(): void
    {
        $lightRain = self::fromFixture('one-call/one-day/success.json', 1);
        $rain = self::fromFixture('one-call/one-day/rain.json');
        $snow = self::fromFixture('one-call/one-day/snow.json');

        self::assertSame(0.09, $lightRain->rain());
        self::assertSame('Rain', $rain->conditions()[0]->group());
        self::assertSame(17.92, $rain->rain());
        self::assertNull($rain->snow());
        self::assertSame('Snow', $snow->conditions()[0]->group());
        self::assertSame(60.98, $snow->snow());
        self::assertNull($snow->rain());
    }

    public function testHydratesHistoricalAndForecastPeriodsWithoutClassification(): void
    {
        $historical = self::fromFixture('one-call/one-day/history.json');
        $forecast = self::fromFixture('one-call/one-day/history.json', 2);

        self::assertSame(1785456000, $historical->dateTime()?->getTimestamp());
        self::assertNull($historical->precipitationProbability());
        self::assertNull($historical->precipitationProbabilityWithUnit());
        self::assertSame(1785628800, $forecast->dateTime()?->getTimestamp());
        self::assertSame(0.0, $forecast->precipitationProbability());
    }

    public function testHydratesDocumentedFieldsAbsentFromCapturedPeriods(): void
    {
        $period = Period::fromArray([
            'dew_point' => 16.5,
            'visibility' => 10000,
            'wind_gust' => 8.2,
            'alerts' => ['alert-id'],
        ]);

        self::assertSame(16.5, $period->dewPoint());
        self::assertSame('16.5 °C', $period->dewPointWithUnit());
        self::assertSame(10000, $period->visibility());
        self::assertSame('10000 m', $period->visibilityWithUnit());
        self::assertSame(8.2, $period->wind()?->gust());
        self::assertSame(['alert-id'], $period->alertIds());
    }

    public function testRetainsUnitsFromHydrationContext(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $period = Period::fromArray([
            'temp' => ['day' => 72.5],
            'feels_like' => ['day' => 71],
            'dew_point' => 60,
            'wind_speed' => 10,
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $period->temperature()?->dayUnit());
        self::assertSame('72.5 °F', $period->temperature()?->dayWithUnit());
        self::assertSame('71 °F', $period->feelsLikeTemperature()?->dayWithUnit());
        self::assertSame('60 °F', $period->dewPointWithUnit());
        self::assertSame(Unit::MILES_PER_HOUR, $period->wind()?->speedUnit());
        self::assertSame('10 mph', $period->wind()?->speedWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = Period::fromArray([]);

        self::assertNull($missing->dateTime());
        self::assertNull($missing->temperature());
        self::assertNull($missing->feelsLikeTemperature());
        self::assertNull($missing->wind());
        self::assertNull($missing->clouds());
        self::assertSame([], $missing->conditions());
        self::assertNull($missing->rain());
        self::assertNull($missing->snow());
        self::assertSame([], $missing->alertIds());

        $period = Period::fromArray([
            'dt' => null,
            'temp' => ['day' => null, 'unknown' => true],
            'feels_like' => null,
            'weather' => [['icon' => null, 'unknown' => true]],
            'clouds' => null,
            'wind_speed' => null,
            'rain' => null,
            'snow' => null,
            'alerts' => null,
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($period->dateTime());
        self::assertNull($period->temperature()?->day());
        self::assertNull($period->feelsLikeTemperature());
        self::assertCount(1, $period->conditions());
        self::assertNull($period->conditions()[0]->icon());
        self::assertNull($period->clouds()?->coverage());
        self::assertNull($period->wind()?->speed());
        self::assertNull($period->rain());
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
            ['dt' => '1785628800'],
            '"dt" expected int, string received.',
        ];
        yield 'sunrise' => [
            ['sunrise' => '1785649113'],
            '"sunrise" expected int, string received.',
        ];
        yield 'moon phase' => [
            ['moon_phase' => '0.62'],
            '"moon_phase" expected int|float, string received.',
        ];
        yield 'temperature object' => [
            ['temp' => 25.53],
            '"temp" expected array, float received.',
        ];
        yield 'temperature field' => [
            ['temp' => ['day' => '25.53']],
            '"day" expected int|float, string received.',
        ];
        yield 'feels-like object' => [
            ['feels_like' => 25.53],
            '"feels_like" expected array, float received.',
        ];
        yield 'pressure' => [
            ['pressure' => '1015.44'],
            '"pressure" expected int|float, string received.',
        ];
        yield 'humidity' => [
            ['humidity' => 48.5],
            '"humidity" expected int, float received.',
        ];
        yield 'visibility' => [
            ['visibility' => 10000.5],
            '"visibility" expected int, float received.',
        ];
        yield 'wind speed' => [
            ['wind_speed' => '6.17'],
            '"wind_speed" expected int|float, string received.',
        ];
        yield 'cloud coverage' => [
            ['clouds' => 41.5],
            '"clouds" expected int, float received.',
        ];
        yield 'conditions' => [
            ['weather' => 'Clouds'],
            '"weather" expected array, string received.',
        ];
        yield 'condition member' => [
            ['weather' => ['Clouds']],
            '"weather.0" expected array, string received.',
        ];
        yield 'scalar rain' => [
            ['rain' => ['1h' => 1.5]],
            '"rain" expected int|float, array received.',
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
