<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Endpoint\GeocodingEndpoint;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipCodeLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;

class GeocodingEndpointTest extends AbstractTest
{
    // --- BY LOCATION NAME ---

    public function testGeocodingGetByLocationName()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_DIRECT
            )
        );

        $response = $this->givenApi()->getGeocoding()->getByLocationName('lisbon, pt');
        $this->assertLocationListResponse($response);
    }

    public function testGeocodingGetByLocationNameWithBlankValue()
    {
        $this->expectException(ValidationException::class);
        $this->givenApi()->getGeocoding()->getByLocationName('');
    }

    // --- BY ZIP CODE ---

    public function testGeocodingGetByZipCode()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_ZIP
            )
        );

        $response = $this->givenApi()->getGeocoding()->getByZipCode('1000-001', 'pt');
        $this->assertInstanceOf(ZipCodeLocation::class, $response);

        $this->assertSame('1000-001', $response->getZipCode());
        $this->assertSame('Lisbon', $response->getName());
        $this->assertSame('PT', $response->getCountryCode());

        $coordinate = $response->getCoordinate();
        $this->assertInstanceOf(Coordinate::class, $coordinate);
        $this->assertSame(38.7167, $coordinate->getLatitude());
        $this->assertSame(-9.1333, $coordinate->getLongitude());
    }

    #[DataProvider('provideGeocodingGetByZipCodeWithInvalidValueData')]
    public function testGeocodingGetByZipCodeWithInvalidValue(string $zipCode, string $countryCode)
    {
        $this->expectException(ValidationException::class);
        $this->givenApi()->getGeocoding()->getByZipCode($zipCode, $countryCode);
    }

    public static function provideGeocodingGetByZipCodeWithInvalidValueData(): \Generator
    {
        yield 'blank zip code' => ['', 'pt'];
        yield 'blank country code' => ['1000-100', ''];
        yield 'invalid country code' => ['1000-100', 'invalid'];
    }

    // --- BY COORDINATE ---

    public function testGeocodingGetByCoordinate()
    {
        $this->mockHttpClient->addResponse(
            new Response(
                status: 200,
                body: MockResponse::GEOCODING_REVERSE
            )
        );

        $response = $this->givenApi()->getGeocoding()->getByCoordinate(50, 50);
        $this->assertLocationListResponse($response);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidCoordinateData')]
    public function testGeocodingGetByCoordinateWithInvalidCoordinate(
        float $latitude,
        float $longitude,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->givenApi()->getGeocoding()->getByCoordinate($latitude, $longitude);
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidNumResultsData')]
    public function testGeocodingGetByCoordinateWithInvalidNumResults(
        int $numResults,
        string $expectedException
    )
    {
        $this->expectException($expectedException);
        $this->givenApi()->getGeocoding()->getByCoordinate(50, 50, $numResults);
    }

    // --- ASSERT METHODS EXIST ---

    public function testGeocodingMethodsExist()
    {
        $this->assertSame(false, method_exists(GeocodingEndpoint::class, 'withUnitSystem'));
        $this->assertSame(false, method_exists(GeocodingEndpoint::class, 'withLanguage'));
        $this->assertSame(true, method_exists(GeocodingEndpoint::class, 'withCacheTtl'));
    }

    // --- ASSERT RESPONSES ---

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