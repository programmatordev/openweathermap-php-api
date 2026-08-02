<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Weather\Forecast;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\Api\Config\Config;
use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast\Period;
use ProgrammatorDev\OpenWeatherMap\Enum\PartOfDay;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class PeriodTest extends TestCase
{
    public function testHydratesCapturedForecastPeriod(): void
    {
        $period = self::fromFixture('weather/forecast/success.json');

        self::assertSame(1785574800, $period->dateTime()?->getTimestamp());
        self::assertSame('UTC', $period->dateTime()?->getTimezone()->getName());
        self::assertSame(22.54, $period->temperature());
        self::assertSame(Unit::CELSIUS, $period->temperatureUnit());
        self::assertSame('22.54 °C', $period->temperatureWithUnit());
        self::assertSame(22.83, $period->feelsLikeTemperature());
        self::assertSame(22.54, $period->minimumTemperature());
        self::assertSame(23.52, $period->maximumTemperature());
        self::assertSame(1016, $period->pressure());
        self::assertSame('1016 hPa', $period->pressureWithUnit());
        self::assertSame(76, $period->humidity());
        self::assertSame('76 %', $period->humidityWithUnit());
        self::assertSame(1016, $period->seaLevelPressure());
        self::assertSame(1006, $period->groundLevelPressure());
        self::assertSame(18.1, $period->dewPoint());
        self::assertSame(Unit::CELSIUS, $period->dewPointUnit());
        self::assertSame('18.1 °C', $period->dewPointWithUnit());
        self::assertSame(10000, $period->visibility());
        self::assertSame('10000 m', $period->visibilityWithUnit());

        self::assertCount(1, $period->conditions());
        self::assertSame('Clouds', $period->conditions()[0]->group());
        self::assertSame(48, $period->clouds()?->coverage());
        self::assertSame(3.36, $period->wind()?->speed());
        self::assertSame('3.36 m/s', $period->wind()?->speedWithUnit());
        self::assertSame(349, $period->wind()?->direction());
        self::assertSame(4.91, $period->wind()?->gust());

        self::assertSame(0.0, $period->precipitationProbability());
        self::assertNull($period->rain());
        self::assertNull($period->snow());
        self::assertSame(PartOfDay::DAY, $period->partOfDay());
    }

    public function testHydratesConditionalRain(): void
    {
        $period = self::fromFixture('weather/forecast/rain.json');

        self::assertSame('Rain', $period->conditions()[0]->group());
        self::assertSame(1.0, $period->precipitationProbability());
        self::assertSame(5.49, $period->rain()?->lastThreeHours());
        self::assertSame(Unit::MILLIMETER, $period->rain()?->lastThreeHoursUnit());
        self::assertSame('5.49 mm', $period->rain()?->lastThreeHoursWithUnit());
        self::assertNull($period->snow());
    }

    public function testHydratesConditionalSnowAndMissingVisibility(): void
    {
        $period = self::fromFixture('weather/forecast/snow.json');

        self::assertSame('Snow', $period->conditions()[0]->group());
        self::assertSame(3.0, $period->snow()?->lastThreeHours());
        self::assertSame('3 mm', $period->snow()?->lastThreeHoursWithUnit());
        self::assertNull($period->rain());
        self::assertNull($period->visibility());
        self::assertNull($period->visibilityWithUnit());
        self::assertSame(PartOfDay::NIGHT, $period->partOfDay());
    }

    public function testRetainsUnitsFromHydrationContext(): void
    {
        $context = new Context(new Config([
            OpenWeatherMap::OPTION_UNITS => Units::IMPERIAL,
        ]));

        $period = Period::fromArray([
            'main' => [
                'temp' => 72.5,
                'dew_point' => 60.25,
            ],
            'wind' => ['speed' => 10],
        ], $context);

        self::assertSame(Unit::FAHRENHEIT, $period->temperatureUnit());
        self::assertSame('72.5 °F', $period->temperatureWithUnit());
        self::assertSame(Unit::MILES_PER_HOUR, $period->wind()?->speedUnit());
        self::assertSame('10 mph', $period->wind()?->speedWithUnit());
        self::assertSame('60.25 °F', $period->dewPointWithUnit());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        self::assertSame([], Period::fromArray(['weather' => null])->conditions());

        $period = Period::fromArray([
            'dt' => null,
            'main' => [
                'temp' => null,
                'dew_point' => null,
            ],
            'weather' => [['icon' => null]],
            'clouds' => ['all' => null],
            'wind' => null,
            'rain' => ['3h' => null, 'unknown' => true],
            'snow' => null,
            'sys' => null,
            'dt_txt' => new \stdClass(),
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($period->dateTime());
        self::assertNull($period->temperature());
        self::assertNull($period->temperatureWithUnit());
        self::assertNull($period->feelsLikeTemperature());
        self::assertNull($period->pressure());
        self::assertNull($period->dewPoint());
        self::assertNull($period->dewPointWithUnit());
        self::assertCount(1, $period->conditions());
        self::assertNull($period->conditions()[0]->icon());
        self::assertNull($period->clouds()?->coverage());
        self::assertNull($period->wind());
        self::assertNull($period->visibility());
        self::assertNull($period->precipitationProbability());
        self::assertNull($period->rain()?->lastThreeHours());
        self::assertNull($period->rain()?->lastThreeHoursWithUnit());
        self::assertNull($period->snow());
        self::assertNull($period->partOfDay());
    }

    public function testRejectsUnknownPartOfDay(): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(
            '"sys.pod" expected "d" or "n", "x" received.',
        );

        Period::fromArray(['sys' => ['pod' => 'x']]);
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

        Period::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'forecast time' => [['dt' => '1785574800'], 'dt', 'int', 'string'];
        yield 'measurements' => [['main' => 'invalid'], 'main', 'array', 'string'];
        yield 'temperature' => [['main' => ['temp' => '22.5']], 'main.temp', 'int|float', 'string'];
        yield 'dew point' => [['main' => ['dew_point' => '18.1']], 'main.dew_point', 'int|float', 'string'];
        yield 'conditions' => [['weather' => 'Clouds'], 'weather', 'array', 'string'];
        yield 'condition member' => [['weather' => ['Clouds']], 'weather.0', 'array', 'string'];
        yield 'clouds' => [['clouds' => ['all' => 48.5]], 'all', 'int', 'float'];
        yield 'wind' => [['wind' => ['speed' => '3.5']], 'speed', 'int|float', 'string'];
        yield 'visibility' => [['visibility' => 10000.5], 'visibility', 'int', 'float'];
        yield 'precipitation probability' => [['pop' => '1'], 'pop', 'int|float', 'string'];
        yield 'rain' => [['rain' => ['3h' => '5.49']], '3h', 'int|float', 'string'];
        yield 'system' => [['sys' => 'invalid'], 'sys', 'array', 'string'];
        yield 'part of day' => [['sys' => ['pod' => 1]], 'sys.pod', 'string', 'int'];
    }

    private static function fromFixture(string $path): Period
    {
        $response = Fixture::json($path);

        return Period::fromArray($response['list'][0]);
    }
}
