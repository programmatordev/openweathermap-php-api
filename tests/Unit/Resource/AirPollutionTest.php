<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Current;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\History;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class AirPollutionTest extends ApiTestCase
{
    public function testGetsCurrentAirPollutionByCoordinates(): void
    {
        $this->respondWithFixture('air-pollution/current/good.json');

        $current = $this->api->airPollution()->current(
            latitude: -33.8688,
            longitude: 151.2093,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Current::class, $current);
        self::assertSame(AirQualityIndex::GOOD, $current->airQualityIndex());
        self::assertSame(-33.8679, $current->coordinates()?->latitude());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/2.5/air_pollution', $request->getUri()->getPath());
        self::assertSame([
            'lat' => '-33.8688',
            'lon' => '151.2093',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsAirPollutionForecastByCoordinates(): void
    {
        $this->respondWithFixture('air-pollution/forecast/good-to-moderate.json');

        $forecast = $this->api->airPollution()->forecast(
            latitude: 28.6139,
            longitude: 77.209,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Forecast::class, $forecast);
        self::assertCount(96, $forecast->periods());
        self::assertSame(1785614400, $forecast->periods()[0]->forecastAt()?->getTimestamp());
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            '/data/2.5/air_pollution/forecast',
            $request->getUri()->getPath(),
        );
        self::assertSame([
            'lat' => '28.6139',
            'lon' => '77.209',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testGetsHistoricalAirPollutionByCoordinatesAndDateRange(): void
    {
        $this->respondWithFixture('air-pollution/history/success.json');

        $history = $this->api->airPollution()->history(
            latitude: 38.7223,
            longitude: -9.1393,
            start: new \DateTimeImmutable('@1782864000'),
            end: new \DateTimeImmutable('@1782950400'),
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(History::class, $history);
        self::assertCount(25, $history->periods());
        self::assertSame(1782864000, $history->periods()[0]->observedAt()?->getTimestamp());
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            '/data/2.5/air_pollution/history',
            $request->getUri()->getPath(),
        );
        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1782864000',
            'end' => '1782950400',
            'appid' => 'api-key',
        ], $this->query($request));
    }

    public function testHistoryAllowsEqualRangeBoundaries(): void
    {
        $this->respondWithFixture('air-pollution/history/empty.json');

        $boundary = new \DateTimeImmutable('@1604188800');

        $this->api->airPollution()->history(
            latitude: 38.7223,
            longitude: -9.1393,
            start: $boundary,
            end: $boundary,
        );

        self::assertSame([
            'lat' => '38.7223',
            'lon' => '-9.1393',
            'start' => '1604188800',
            'end' => '1604188800',
            'appid' => 'api-key',
        ], $this->query($this->client->getLastRequest()));
    }

    public function testHistoryRejectsReversedRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The end date must be after or equal to the start date.',
        );

        $this->api->airPollution()->history(
            latitude: 38.7223,
            longitude: -9.1393,
            start: new \DateTimeImmutable('@1782950400'),
            end: new \DateTimeImmutable('@1782864000'),
        );
    }

    public function testHistoryRejectsFutureEnd(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The end date must not be in the future.');

        $this->api->airPollution()->history(
            latitude: 38.7223,
            longitude: -9.1393,
            start: new \DateTimeImmutable('@0'),
            end: new \DateTimeImmutable(sprintf('@%d', time() + 60)),
        );
    }

    #[DataProvider('invalidCoordinates')]
    public function testCurrentRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->airPollution()->current($latitude, $longitude);
    }

    #[DataProvider('invalidCoordinates')]
    public function testForecastRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->airPollution()->forecast($latitude, $longitude);
    }

    #[DataProvider('invalidCoordinates')]
    public function testHistoryRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->airPollution()->history(
            $latitude,
            $longitude,
            new \DateTimeImmutable('@1604188800'),
            new \DateTimeImmutable('@1604192400'),
        );
    }

    public static function invalidCoordinates(): iterable
    {
        yield 'invalid latitude' => [
            90.0001,
            0,
            'Latitude must be a finite number between -90 and 90.',
        ];
        yield 'invalid longitude' => [
            0,
            180.0001,
            'Longitude must be a finite number between -180 and 180.',
        ];
    }
}
