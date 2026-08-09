<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Resource;

use ProgrammatorDev\OpenWeatherMap\Entity\Stations\Station;
use ProgrammatorDev\OpenWeatherMap\Test\Support\ApiTestCase;

final class StationsTest extends ApiTestCase
{
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
}
