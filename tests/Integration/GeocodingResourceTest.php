<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Integration;

use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Test\AbstractTest;
use ProgrammatorDev\OpenWeatherMap\Test\MockResponse;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestCollectionResponseTrait;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestItemResponseTrait;

class GeocodingResourceTest extends AbstractTest
{
    use TestItemResponseTrait;
    use TestCollectionResponseTrait;

    public static function provideItemResponseData(): \Generator
    {
        yield 'get by zip code' => [
            ZipLocation::class,
            MockResponse::GEOCODING_ZIP,
            'geocoding',
            'getByZipCode',
            ['1000-001', 'pt']
        ];
    }

    public static function provideCollectionResponseData(): \Generator
    {
        yield 'get by location name' => [
            Location::class,
            MockResponse::GEOCODING_DIRECT,
            'geocoding',
            'getByLocationName',
            ['test']
        ];
        yield 'get by coordinate' => [
            Location::class,
            MockResponse::GEOCODING_REVERSE,
            'geocoding',
            'getByCoordinate',
            [50, 50]
        ];
    }
}