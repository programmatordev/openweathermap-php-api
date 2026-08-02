<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Current;
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

    #[DataProvider('invalidCoordinates')]
    public function testRejectsInvalidCoordinates(
        float $latitude,
        float $longitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->airPollution()->current($latitude, $longitude);
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
