<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Request\Stations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Measurement;

final class MeasurementTest extends TestCase
{
    public function testMapsDocumentedScalarMeasurements(): void
    {
        $measurement = new Measurement(
            stationId: ' station-id ',
            dateTime: new \DateTimeImmutable('2026-08-08T23:22:30+02:00'),
            temperature: 19.5,
            windSpeed: 2.4,
            windGust: 4.1,
            windDirection: 180,
            pressure: 1012,
            humidity: 68,
            rainLastHour: 0.2,
            rainLastSixHours: 0.4,
            rainLastTwentyFourHours: 0.6,
            snowLastHour: 0.0,
            snowLastSixHours: 0.1,
            snowLastTwentyFourHours: 0.3,
            dewPoint: 12.5,
            humidex: 20.1,
            heatIndex: 19.8,
            visibilityDistance: 10,
            visibilityPrefix: ' N ',
        );

        self::assertSame('station-id', $measurement->stationId());
        self::assertSame('UTC', $measurement->dateTime()->getTimezone()->getName());
        self::assertSame(19.5, $measurement->temperature());
        self::assertSame(2.4, $measurement->windSpeed());
        self::assertSame(4.1, $measurement->windGust());
        self::assertSame(180, $measurement->windDirection());
        self::assertSame(1012.0, $measurement->pressure());
        self::assertSame(68.0, $measurement->humidity());
        self::assertSame(0.2, $measurement->rainLastHour());
        self::assertSame(0.4, $measurement->rainLastSixHours());
        self::assertSame(0.6, $measurement->rainLastTwentyFourHours());
        self::assertSame(0.0, $measurement->snowLastHour());
        self::assertSame(0.1, $measurement->snowLastSixHours());
        self::assertSame(0.3, $measurement->snowLastTwentyFourHours());
        self::assertSame(12.5, $measurement->dewPoint());
        self::assertSame(20.1, $measurement->humidex());
        self::assertSame(19.8, $measurement->heatIndex());
        self::assertSame(10.0, $measurement->visibilityDistance());
        self::assertSame('N', $measurement->visibilityPrefix());
        self::assertSame([
            'station_id' => 'station-id',
            'dt' => 1786224150,
            'temperature' => 19.5,
            'wind_speed' => 2.4,
            'wind_gust' => 4.1,
            'wind_deg' => 180,
            'pressure' => 1012.0,
            'humidity' => 68.0,
            'rain_1h' => 0.2,
            'rain_6h' => 0.4,
            'rain_24h' => 0.6,
            'snow_1h' => 0.0,
            'snow_6h' => 0.1,
            'snow_24h' => 0.3,
            'dew_point' => 12.5,
            'humidex' => 20.1,
            'heat_index' => 19.8,
            'visibility_distance' => 10.0,
            'visibility_prefix' => 'N',
        ], $measurement->toArray());
    }

    public function testOmitsUnavailableScalarMeasurements(): void
    {
        $measurement = new Measurement(
            stationId: 'station-id',
            dateTime: new \DateTimeImmutable('@1786231350'),
        );

        self::assertSame([
            'station_id' => 'station-id',
            'dt' => 1786231350,
        ], $measurement->toArray());
    }

    public function testRejectsABlankStationIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The station ID must be a non-empty string.',
        );

        new Measurement('   ', new \DateTimeImmutable());
    }

    #[DataProvider('invalidWindDirections')]
    public function testRejectsAnInvalidWindDirection(int $windDirection): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The wind direction must be between 0 and 360.',
        );

        new Measurement(
            'station-id',
            new \DateTimeImmutable(),
            windDirection: $windDirection,
        );
    }

    public static function invalidWindDirections(): iterable
    {
        yield 'below zero' => [-1];
        yield 'above 360' => [361];
    }

    public function testRejectsANonFiniteScalarMeasurement(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The temperature must be a finite number.',
        );

        new Measurement(
            'station-id',
            new \DateTimeImmutable(),
            temperature: INF,
        );
    }

    public function testRejectsABlankVisibilityPrefix(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The visibility prefix must be a non-empty string.',
        );

        new Measurement(
            'station-id',
            new \DateTimeImmutable(),
            visibilityPrefix: '   ',
        );
    }
}
