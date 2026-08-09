<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\Station;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\CloudLayer;
use ProgrammatorDev\OpenWeatherMap\Request\Stations\Measurement;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class StationsTest extends ApiTestCase
{
    public function testCreatesAStation(): void
    {
        $this->respondWithFixture('stations/register.json', status: 201);

        $station = $this->api->stations()->create(
            externalId: ' openweathermap-php-api-fixture ',
            name: ' OpenWeatherMap PHP API Fixture ',
            latitude: 38.7223,
            longitude: -9.1393,
            altitude: 100,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Station::class, $station);
        self::assertSame('6a779284adde3b0001343e02', $station->id());
        self::assertSame('user-fixture', $station->userId());
        self::assertSame(5, $station->sourceType());
        self::assertSame('POST', $request->getMethod());
        self::assertSame('/data/3.0/stations', $request->getUri()->getPath());
        self::assertSame(['appid' => 'api-key'], $this->query($request));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame([
            'external_id' => 'openweathermap-php-api-fixture',
            'name' => 'OpenWeatherMap PHP API Fixture',
            'latitude' => 38.7223,
            'longitude' => -9.1393,
            'altitude' => 100,
        ], json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testListsStations(): void
    {
        $this->respondWithFixture('stations/list.json');

        $stations = $this->api->stations()->all();
        $request = $this->client->getLastRequest();

        self::assertCount(1, $stations);
        self::assertContainsOnlyInstancesOf(Station::class, $stations);
        self::assertSame('6a779284adde3b0001343e02', $stations[0]->id());
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/data/3.0/stations', $request->getUri()->getPath());
        self::assertSame(['appid' => 'api-key'], $this->query($request));
    }

    public function testUpdatesAStation(): void
    {
        $this->respondWithFixture('stations/update.json');

        $station = $this->api->stations()->update(
            id: ' 6a779284adde3b0001343e02 ',
            externalId: ' openweathermap-php-api-fixture-updated ',
            name: ' Updated OpenWeatherMap PHP API Fixture ',
            latitude: 38.72,
            longitude: -9.14,
            altitude: 110,
        );
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Station::class, $station);
        self::assertSame('openweathermap-php-api-fixture-updated', $station->externalId());
        self::assertSame('PUT', $request->getMethod());
        self::assertSame(
            '/data/3.0/stations/6a779284adde3b0001343e02',
            $request->getUri()->getPath(),
        );
        self::assertSame(['appid' => 'api-key'], $this->query($request));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame([
            'external_id' => 'openweathermap-php-api-fixture-updated',
            'name' => 'Updated OpenWeatherMap PHP API Fixture',
            'latitude' => 38.72,
            'longitude' => -9.14,
            'altitude' => 110,
        ], json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testFindsAStation(): void
    {
        $this->respondWithFixture('stations/retrieve.json');

        $station = $this->api->stations()->find(' 6a779284adde3b0001343e02 ');
        $request = $this->client->getLastRequest();

        self::assertInstanceOf(Station::class, $station);
        self::assertSame('OpenWeatherMap PHP API Fixture', $station->name());
        self::assertSame('GET', $request->getMethod());
        self::assertSame(
            '/data/3.0/stations/6a779284adde3b0001343e02',
            $request->getUri()->getPath(),
        );
        self::assertSame(['appid' => 'api-key'], $this->query($request));
    }

    public function testRejectsABlankStationIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The station ID must be a non-empty string.',
        );

        $this->api->stations()->find('   ');
    }

    public function testRejectsABlankStationIdentifierWhenUpdating(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The station ID must be a non-empty string.',
        );

        $this->api->stations()->update(
            id: '   ',
            externalId: 'station',
            name: 'Station',
            latitude: 0,
            longitude: 0,
            altitude: 0,
        );
    }

    public function testDeletesAStation(): void
    {
        $this->respondWithFixture('stations/delete.empty', status: 204);

        $this->api->stations()->delete(' 6a779284adde3b0001343e02 ');
        $request = $this->client->getLastRequest();

        self::assertSame('DELETE', $request->getMethod());
        self::assertSame(
            '/data/3.0/stations/6a779284adde3b0001343e02',
            $request->getUri()->getPath(),
        );
        self::assertSame(['appid' => 'api-key'], $this->query($request));
        self::assertSame('', (string) $request->getBody());
    }

    public function testRejectsABlankStationIdentifierWhenDeleting(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The station ID must be a non-empty string.',
        );

        $this->api->stations()->delete('   ');
    }

    public function testSubmitsAMeasurement(): void
    {
        $this->respondWithFixture('stations/measurements/submit.empty', status: 204);

        $this->api->stations()->submitMeasurement(new Measurement(
            stationId: 'station-id',
            dateTime: new \DateTimeImmutable('@1786231350'),
            temperature: 19.5,
            clouds: [new CloudLayer(condition: 'NSC')],
        ));
        $request = $this->client->getLastRequest();

        self::assertSame('POST', $request->getMethod());
        self::assertSame('/data/3.0/measurements', $request->getUri()->getPath());
        self::assertSame(['appid' => 'api-key'], $this->query($request));
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame([[
            'station_id' => 'station-id',
            'dt' => 1786231350,
            'temperature' => 19.5,
            'clouds' => [['condition' => 'NSC']],
        ]], json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testSubmitsMultipleMeasurements(): void
    {
        $this->respondWithFixture('stations/measurements/submit.empty', status: 204);

        $measurements = [
            new Measurement(
                stationId: '6a77b80aadde3b0001343e08',
                dateTime: new \DateTimeImmutable('@1786143600'),
                temperature: 19.5,
                windSpeed: 2.4,
                windGust: 4.1,
                windDirection: 180,
                pressure: 1012,
                humidity: 68,
                rainLastHour: 0.2,
            ),
            new Measurement(
                stationId: '6a77b80aadde3b0001343e08',
                dateTime: new \DateTimeImmutable('@1786228200'),
                temperature: 20.5,
                windSpeed: 3.2,
                windGust: 5.3,
                windDirection: 200,
                pressure: 1013,
                humidity: 64,
                rainLastHour: 0.4,
            ),
            new Measurement(
                stationId: '6a77b80aadde3b0001343e08',
                dateTime: new \DateTimeImmutable('@1786230720'),
                temperature: 21.5,
                windSpeed: 4,
                windGust: 6.5,
                windDirection: 220,
                pressure: 1014,
                humidity: 60,
                rainLastHour: 0.6,
            ),
        ];

        $this->api->stations()->submitMeasurements($measurements);
        $request = $this->client->getLastRequest();

        self::assertSame(
            [
                [
                    'station_id' => '6a77b80aadde3b0001343e08',
                    'dt' => 1786143600,
                    'temperature' => 19.5,
                    'wind_speed' => 2.4,
                    'wind_gust' => 4.1,
                    'wind_deg' => 180,
                    'pressure' => 1012,
                    'humidity' => 68,
                    'rain_1h' => 0.2,
                ],
                [
                    'station_id' => '6a77b80aadde3b0001343e08',
                    'dt' => 1786228200,
                    'temperature' => 20.5,
                    'wind_speed' => 3.2,
                    'wind_gust' => 5.3,
                    'wind_deg' => 200,
                    'pressure' => 1013,
                    'humidity' => 64,
                    'rain_1h' => 0.4,
                ],
                [
                    'station_id' => '6a77b80aadde3b0001343e08',
                    'dt' => 1786230720,
                    'temperature' => 21.5,
                    'wind_speed' => 4,
                    'wind_gust' => 6.5,
                    'wind_deg' => 220,
                    'pressure' => 1014,
                    'humidity' => 60,
                    'rain_1h' => 0.6,
                ],
            ],
            json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testRejectsAnEmptyMeasurementBatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The station measurements must not be empty.',
        );

        $this->api->stations()->submitMeasurements([]);
    }

    public function testRejectsAnInvalidMeasurementBatchItem(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The station measurement at index 1 must be an instance of',
        );

        $this->api->stations()->submitMeasurements([
            new Measurement('station-id', new \DateTimeImmutable()),
            'invalid',
        ]);
    }

    #[DataProvider('invalidCreationArguments')]
    public function testRejectsInvalidCreationArguments(
        string $externalId,
        string $name,
        float $latitude,
        float $longitude,
        float $altitude,
        string $message,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $this->api->stations()->create(
            $externalId,
            $name,
            $latitude,
            $longitude,
            $altitude,
        );
    }

    public static function invalidCreationArguments(): iterable
    {
        yield 'blank external identifier' => [
            '   ',
            'Station',
            0,
            0,
            0,
            'The external station ID must be a non-empty string.',
        ];
        yield 'blank name' => [
            'station',
            '   ',
            0,
            0,
            0,
            'The station name must be a non-empty string.',
        ];
        yield 'invalid latitude' => [
            'station',
            'Station',
            90.0001,
            0,
            0,
            'Latitude must be a finite number between -90 and 90.',
        ];
        yield 'invalid longitude' => [
            'station',
            'Station',
            0,
            180.0001,
            0,
            'Longitude must be a finite number between -180 and 180.',
        ];
        yield 'non-finite altitude' => [
            'station',
            'Station',
            0,
            0,
            INF,
            'The station altitude must be a finite number.',
        ];
    }
}
