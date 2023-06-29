<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipCodeLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;
use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateNumResultsTrait;

class Geocoding extends AbstractEndpoint
{
    use CreateEntityListTrait;
    use ValidateCoordinateTrait;
    use ValidateNumResultsTrait;

    private const NUM_RESULTS = 5;

    private string $urlGeocodingDirect = 'https://api.openweathermap.org/geo/1.0/direct';

    private string $urlGeocodingZipCode = 'https://api.openweathermap.org/geo/1.0/zip';

    private string $urlGeocodingReverse = 'https://api.openweathermap.org/geo/1.0/reverse';

    /**
     * @return Location[]
     * @throws Exception
     * @throws InvalidNumResultsException
     */
    public function getCoordinatesByLocationName(string $locationName, int $numResults = self::NUM_RESULTS): array
    {
        $this->validateNumResults($numResults);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlGeocodingDirect,
            query: [
                'q' => $locationName,
                'limit' => $numResults
            ]
        );

        return $this->createEntityList($data, Location::class);
    }

    /**
     * @throws Exception
     */
    public function getCoordinatesByZipCode(string $zipCode, string $countryCode): ZipCodeLocation
    {
        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlGeocodingZipCode,
            query: [
                'zip' => \sprintf('%s,%s', $zipCode, $countryCode)
            ]
        );

        return new ZipCodeLocation($data);
    }

    /**
     * @return Location[]
     * @throws Exception
     * @throws InvalidCoordinateException
     * @throws InvalidNumResultsException
     */
    public function getLocationNameByCoordinates(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): array
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateNumResults($numResults);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlGeocodingReverse,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'limit' => $numResults
            ]
        );

        return $this->createEntityList($data, Location::class);
    }
}