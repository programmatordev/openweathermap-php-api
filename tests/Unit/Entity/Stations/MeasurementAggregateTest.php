<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Entity\Stations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate;
use ProgrammatorDev\OpenWeatherMap\Enum\AggregationInterval;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Test\Support\Fixture;

final class MeasurementAggregateTest extends TestCase
{
    public function testHydratesCapturedMinuteAggregate(): void
    {
        $aggregate = MeasurementAggregate::fromArray(
            Fixture::json('stations/measurements/aggregate-minute-success.json')[0],
        );

        self::assertSame(AggregationInterval::MINUTE, $aggregate->interval());
        self::assertSame(1786231380, $aggregate->dateTime()?->getTimestamp());
        self::assertSame('UTC', $aggregate->dateTime()?->getTimezone()->getName());
        self::assertSame('6a77ba36adde3b0001343e09', $aggregate->stationId());

        self::assertSame(19.5, $aggregate->temperature()?->minimum());
        self::assertSame(Unit::CELSIUS, $aggregate->temperature()?->minimumUnit());
        self::assertSame('19.5 °C', $aggregate->temperature()?->minimumWithUnit());
        self::assertSame(21.5, $aggregate->temperature()?->maximum());
        self::assertSame('21.5 °C', $aggregate->temperature()?->maximumWithUnit());
        self::assertSame(20.5, $aggregate->temperature()?->average());
        self::assertSame('20.5 °C', $aggregate->temperature()?->averageWithUnit());
        self::assertSame(3, $aggregate->temperature()?->weight());

        self::assertSame(64.0, $aggregate->humidity()?->average());
        self::assertSame(Unit::PERCENT, $aggregate->humidity()?->averageUnit());
        self::assertSame('64 %', $aggregate->humidity()?->averageWithUnit());
        self::assertSame(3, $aggregate->humidity()?->weight());

        self::assertSame(203.4, $aggregate->wind()?->direction());
        self::assertSame(Unit::DEGREE, $aggregate->wind()?->directionUnit());
        self::assertSame('203.4 °', $aggregate->wind()?->directionWithUnit());
        self::assertSame(3.08, $aggregate->wind()?->speed());
        self::assertSame(Unit::METERS_PER_SECOND, $aggregate->wind()?->speedUnit());
        self::assertSame('3.08 m/s', $aggregate->wind()?->speedWithUnit());

        self::assertSame(1012.0, $aggregate->pressure()?->minimum());
        self::assertSame(Unit::HECTOPASCAL, $aggregate->pressure()?->minimumUnit());
        self::assertSame('1012 hPa', $aggregate->pressure()?->minimumWithUnit());
        self::assertSame(1014.0, $aggregate->pressure()?->maximum());
        self::assertSame('1014 hPa', $aggregate->pressure()?->maximumWithUnit());
        self::assertSame(1013.0, $aggregate->pressure()?->average());
        self::assertSame('1013 hPa', $aggregate->pressure()?->averageWithUnit());
        self::assertSame(3, $aggregate->pressure()?->weight());

        self::assertNotNull($aggregate->precipitation());
        self::assertNull($aggregate->precipitation()?->rain());
        self::assertNull($aggregate->precipitation()?->rainWithUnit());
        self::assertNull($aggregate->precipitation()?->snow());
    }

    public function testHydratesCapturedHourAndDayAggregates(): void
    {
        $hour = MeasurementAggregate::fromArray(
            Fixture::json('stations/measurements/aggregate-hour-success.json')[0],
        );
        $day = MeasurementAggregate::fromArray(
            Fixture::json('stations/measurements/aggregate-day-success.json')[0],
        );

        self::assertSame(AggregationInterval::HOUR, $hour->interval());
        self::assertSame(AggregationInterval::DAY, $day->interval());
        self::assertSame(1786233600, $hour->dateTime()?->getTimestamp());
        self::assertSame(1786233600, $day->dateTime()?->getTimestamp());
        self::assertSame(0.6, $hour->precipitation()?->rain());
        self::assertSame(Unit::MILLIMETER, $hour->precipitation()?->rainUnit());
        self::assertSame('0.6 mm', $hour->precipitation()?->rainWithUnit());
        self::assertSame(0.6, $day->precipitation()?->rain());
    }

    public function testToleratesMissingNullUnknownAndPartialFields(): void
    {
        $missing = MeasurementAggregate::fromArray([]);

        self::assertNull($missing->interval());
        self::assertNull($missing->dateTime());
        self::assertNull($missing->stationId());
        self::assertNull($missing->temperature());
        self::assertNull($missing->humidity());
        self::assertNull($missing->wind());
        self::assertNull($missing->pressure());
        self::assertNull($missing->precipitation());

        $partial = MeasurementAggregate::fromArray([
            'type' => null,
            'date' => null,
            'station_id' => null,
            'temp' => ['average' => null, 'unknown' => new \stdClass()],
            'humidity' => [],
            'wind' => ['speed' => null],
            'pressure' => [],
            'precipitation' => ['rain' => null, 'snow' => 1.2],
            'unknown' => new \stdClass(),
        ]);

        self::assertNull($partial->temperature()?->average());
        self::assertNull($partial->humidity()?->average());
        self::assertNull($partial->wind()?->speed());
        self::assertNull($partial->pressure()?->average());
        self::assertNull($partial->precipitation()?->rain());
        self::assertSame(1.2, $partial->precipitation()?->snow());
        self::assertSame(Unit::MILLIMETER, $partial->precipitation()?->snowUnit());
        self::assertSame('1.2 mm', $partial->precipitation()?->snowWithUnit());
    }

    public function testRejectsAnUnknownAggregationInterval(): void
    {
        $this->expectException(HydrationException::class);
        $this->expectExceptionMessage(
            '"type" expected one of m, h, or d, "week" received.',
        );

        MeasurementAggregate::fromArray(['type' => 'week']);
    }

    #[DataProvider('invalidFields')]
    public function testRejectsInvalidKnownFields(
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

        MeasurementAggregate::fromArray($data);
    }

    public static function invalidFields(): iterable
    {
        yield 'interval' => [['type' => 1], 'type', 'string', 'int'];
        yield 'date' => [['date' => '1786231380'], 'date', 'int', 'string'];
        yield 'station identifier' => [['station_id' => 1], 'station_id', 'string', 'int'];
        yield 'temperature container' => [['temp' => 'invalid'], 'temp', 'array', 'string'];
        yield 'temperature minimum' => [['temp' => ['min' => '19.5']], 'min', 'int|float', 'string'];
        yield 'temperature weight' => [['temp' => ['weight' => 3.0]], 'weight', 'int', 'float'];
        yield 'humidity average' => [['humidity' => ['average' => '64']], 'average', 'int|float', 'string'];
        yield 'wind direction' => [['wind' => ['deg' => '203.4']], 'deg', 'int|float', 'string'];
        yield 'pressure average' => [['pressure' => ['average' => '1013']], 'average', 'int|float', 'string'];
        yield 'precipitation rain' => [['precipitation' => ['rain' => '0.6']], 'rain', 'int|float', 'string'];
    }
}
