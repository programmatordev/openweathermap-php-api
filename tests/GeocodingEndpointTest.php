<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipCodeLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointInvalidResponseTrait;
use ProgrammatorDev\OpenWeatherMap\Test\Util\TestEndpointSuccessResponseTrait;

class GeocodingEndpointTest extends AbstractTest
{
    use TestEndpointSuccessResponseTrait;
    use TestEndpointInvalidResponseTrait;

    public static function provideEndpointSuccessResponseData(): \Generator
    {
        yield 'get by location name' => [
            MockResponse::GEOCODING_DIRECT,
            'geocoding',
            'getByLocationName',
            ['test'],
            'assertGetByLocationResponse'
        ];
        yield 'get by coordinate' => [
            MockResponse::GEOCODING_REVERSE,
            'geocoding',
            'getByCoordinate',
            [50, 50],
            'assertGetByLocationResponse'
        ];
        yield 'get by zip code' => [
            MockResponse::GEOCODING_ZIP,
            'geocoding',
            'getByZipCode',
            ['1234-567', 'pt'],
            'assertGetByZipCodeResponse'
        ];
    }

    public static function provideEndpointInvalidResponseData(): \Generator
    {
        yield 'get by location name, blank value' => ['geocoding', 'getByLocationName', ['']];

        yield 'get by zip code, blank zip code' => ['geocoding', 'getByZipCode', ['', 'pt']];
        yield 'get by zip code, blank country code' => ['geocoding', 'getByZipCode', ['1234-567', '']];
        yield 'get by zip code, invalid country code' => ['geocoding', 'getByZipCode', ['1234-567', 'invalid']];

        yield 'get by coordinate, latitude lower than -90' => ['geocoding', 'getByCoordinate', [-91, 50]];
        yield 'get by coordinate, latitude greater than 90' => ['geocoding', 'getByCoordinate', [91, 50]];
        yield 'get by coordinate, longitude lower than -180' => ['geocoding', 'getByCoordinate', [50, -181]];
        yield 'get by coordinate, longitude greater than 180' => ['geocoding', 'getByCoordinate', [50, 181]];
        yield 'get by coordinate, zero num results' => ['geocoding', 'getByCoordinate', [50, 50, 0]];
        yield 'get by coordinate, negative num results' => ['geocoding', 'getByCoordinate', [50, 50, -1]];
    }

    public function testGeocodingMethodsExist()
    {
        $this->assertSame(false, method_exists(GeocodingEndpoint::class, 'withUnitSystem'));
        $this->assertSame(false, method_exists(GeocodingEndpoint::class, 'withLanguage'));
        $this->assertSame(true, method_exists(GeocodingEndpoint::class, 'withCacheTtl'));
    }

    /**  @param Location[] $locations */
    private function assertGetByLocationResponse(array $locations): void
    {
        $this->assertContainsOnlyInstancesOf(Location::class, $locations);

        $location = $locations[0];
        $this->assertSame(null, $location->getId());
        $this->assertSame('Lisbon', $location->getName());
        $this->assertSame(null, $location->getState());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertIsArray($location->getLocalNames());
        $this->assertSame('Lisboa', $location->getLocalName('pt'));
        $this->assertSame(null, $location->getPopulation());
        $this->assertSame(null, $location->getTimezone());
        $this->assertSame(null, $location->getSunriseAt());
        $this->assertSame(null, $location->getSunsetAt());

        $coordinate = $locations[0]->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7077507, $coordinate->getLatitude());
        $this->assertSame(-9.1365919, $coordinate->getLongitude());
    }

    private function assertGetByZipCodeResponse(ZipCodeLocation $zipCodeLocation): void
    {
        $this->assertSame('1000-001', $zipCodeLocation->getZipCode());
        $this->assertSame('Lisbon', $zipCodeLocation->getName());
        $this->assertSame('PT', $zipCodeLocation->getCountryCode());

        $coordinate = $zipCodeLocation->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7167, $coordinate->getLatitude());
        $this->assertSame(-9.1333, $coordinate->getLongitude());
    }
}