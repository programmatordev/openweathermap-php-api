<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipCodeLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class GeocodingEndpointTest extends AbstractTest
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
        $this->assertLocationListResponse($response);
    }

    public function testGeocodingGetCoordinatesByLocationNameWithBlankValue()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->getApi()->getGeocoding()->getCoordinatesByLocationName('');
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
        $this->assertInstanceOf(ZipCodeLocation::class, $response);

        $this->assertSame('1000-001', $response->getZipCode());
        $this->assertSame('Lisbon', $response->getName());
        $this->assertSame('PT', $response->getCountryCode());

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7167, $coordinate->getLatitude());
        $this->assertSame(-9.1333, $coordinate->getLongitude());
    }

    #[DataProvider('provideGeocodingGetCoordinatesByZipCodeWithBlankValueData')]
    public function testGeocodingGetCoordinatesByZipCodeWithBlankValue(string $zipCode, string $countryCode)
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->getApi()->getGeocoding()->getCoordinatesByZipCode($zipCode, $countryCode);
    }

    public static function provideGeocodingGetCoordinatesByZipCodeWithBlankValueData(): \Generator
    {
        yield 'blank zip code' => ['', 'pt'];
        yield 'blank country code' => ['1000-100', ''];
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
        $this->assertLocationListResponse($response);
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

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidNumResultsData')]
    public function testGeocodingGetLocationNameByCoordinatesWithInvalidNumResults(
        int $numResults,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->getApi()->getGeocoding()->getLocationNameByCoordinates(38.7077507, -9.1365919, $numResults);
    }

    /**
     * @param Location[] $response
     */
    private function assertLocationListResponse(array $response): void
    {
        $this->assertContainsOnlyInstancesOf(Location::class, $response);

        $location = $response[0];
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

        $coordinate = $response[0]->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7077507, $coordinate->getLatitude());
        $this->assertSame(-9.1365919, $coordinate->getLongitude());
    }
}