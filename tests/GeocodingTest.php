<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipLocation;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class GeocodingTest extends AbstractTest
{
    public function testGeocodingGetCoordinatesByLocationName()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_DIRECT
            )
        );

        $response = $this->getApi()->getGeocoding()->getCoordinatesByLocationName('lisbon, pt');
        $this->assertContainsOnlyInstancesOf(Location::class, $response);

        $location = $response[0];
        $this->assertSame('Lisbon', $location->getName());
        $this->assertIsArray($location->getLocalNames());
        $this->assertSame('Lisboa', $location->getLocalName('pt'));
        $this->assertSame('Lisbon', $location->getLocalFeatureName());
        $this->assertSame('Lisbon', $location->getLocalAsciiName());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertSame(null, $location->getState());

        $coordinate = $response[0]->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7077507, $coordinate->getLatitude());
        $this->assertSame(-9.1365919, $coordinate->getLongitude());
    }

    public function testGeocodingGetCoordinatesByZipCode()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_ZIP
            )
        );

        $response = $this->getApi()->getGeocoding()->getCoordinatesByZipCode('1000-001', 'pt');
        $this->assertInstanceOf(ZipLocation::class, $response);

        $this->assertSame('1000-001', $response->getZipCode());
        $this->assertSame('Lisbon', $response->getName());
        $this->assertSame('PT', $response->getCountryCode());

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7167, $coordinate->getLatitude());
        $this->assertSame(-9.1333, $coordinate->getLongitude());
    }

    public function testGeocodingGetLocationNameByCoordinates()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_REVERSE
            )
        );

        $response = $this->getApi()->getGeocoding()->getLocationNameByCoordinates(38.7077507, -9.1365919);
        $this->assertContainsOnlyInstancesOf(Location::class, $response);

        $location = $response[0];
        $this->assertSame('Lisbon', $location->getName());
        $this->assertIsArray($location->getLocalNames());
        $this->assertSame('Lisboa', $location->getLocalName('pt'));
        $this->assertSame('Lisbon', $location->getLocalFeatureName());
        $this->assertSame('Lisbon', $location->getLocalAsciiName());
        $this->assertSame('PT', $location->getCountryCode());
        $this->assertSame(null, $location->getState());

        $coordinate = $response[0]->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7077507, $coordinate->getLatitude());
        $this->assertSame(-9.1365919, $coordinate->getLongitude());
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testGeocodingGetLocationNameByCoordinatesWithInvalidCoordinate(
        float $latitude,
        float $longitude,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->getApi()->getGeocoding()->getLocationNameByCoordinates($latitude, $longitude);
    }
}