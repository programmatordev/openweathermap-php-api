<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Value;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Value\Coordinates;

final class CoordinatesTest extends TestCase
{
    public function testItExposesCoordinates(): void
    {
        $coordinates = Coordinates::from(
            latitude: 38.7078,
            longitude: -9.1366,
        );

        self::assertSame(38.7078, $coordinates->latitude());
        self::assertSame(-9.1366, $coordinates->longitude());
    }

    public function testItAcceptsCoordinateBoundaries(): void
    {
        $minimum = Coordinates::from(latitude: -90, longitude: -180);
        $maximum = Coordinates::from(latitude: 90, longitude: 180);

        self::assertSame(-90.0, $minimum->latitude());
        self::assertSame(-180.0, $minimum->longitude());
        self::assertSame(90.0, $maximum->latitude());
        self::assertSame(180.0, $maximum->longitude());
    }

    #[DataProvider('invalidLatitudes')]
    public function testItRejectsInvalidLatitudes(float $latitude): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Latitude must be a finite number between -90 and 90.',
        );

        Coordinates::from($latitude, 0);
    }

    /**
     * @return iterable<string, array{float}>
     */
    public static function invalidLatitudes(): iterable
    {
        yield 'below minimum' => [-90.0001];
        yield 'above maximum' => [90.0001];
        yield 'negative infinity' => [-INF];
        yield 'positive infinity' => [INF];
        yield 'not a number' => [NAN];
    }

    #[DataProvider('invalidLongitudes')]
    public function testItRejectsInvalidLongitudes(float $longitude): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Longitude must be a finite number between -180 and 180.',
        );

        Coordinates::from(0, $longitude);
    }

    /**
     * @return iterable<string, array{float}>
     */
    public static function invalidLongitudes(): iterable
    {
        yield 'below minimum' => [-180.0001];
        yield 'above maximum' => [180.0001];
        yield 'negative infinity' => [-INF];
        yield 'positive infinity' => [INF];
        yield 'not a number' => [NAN];
    }
}
