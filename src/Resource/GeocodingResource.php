<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Method;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Util\EntityTrait;
use Psr\Http\Client\ClientExceptionInterface;

class GeocodingResource extends Resource
{
    use EntityTrait;

    private const NUM_RESULTS = 5;

    /**
     * Get geographical coordinates (latitude, longitude) by using the name of the location (city name or area name)
     *
     * @return Location[]
     * @throws ClientExceptionInterface
     */
    public function getByLocationName(string $locationName, int $numResults = self::NUM_RESULTS): array
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/geo/1.0/direct',
            query: [
                'q' => $locationName,
                'limit' => $numResults
            ]
        );

        return $this->createEntityList(Location::class, $data);
    }

    /**
     * Get geographical coordinates (latitude, longitude) by using the zip/postal code
     *
     * @throws ClientExceptionInterface
     */
    public function getByZipCode(string $zipCode, string $countryCode): ZipLocation
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/geo/1.0/zip',
            query: [
                'zip' => \sprintf('%s,%s', $zipCode, $countryCode)
            ]
        );

        return new ZipLocation($data);
    }

    /**
     * Get the name of the location (city name or area name) by using geographical coordinates (latitude, longitude)
     *
     * @return Location[]
     * @throws ClientExceptionInterface
     */
    public function getByCoordinate(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): array
    {
        $data = $this->api->request(
            method: Method::GET,
            path: '/geo/1.0/reverse',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'limit' => $numResults
            ]
        );

        return $this->createEntityList(Location::class, $data);
    }
}