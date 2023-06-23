<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipLocation;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateCoordinateTrait;

class Geocoding extends AbstractEndpoint
{
    use CreateEntityListTrait;
    use ValidateCoordinateTrait;

    private string $urlDirectGeocoding = 'https://api.openweathermap.org/geo/1.0/direct';

    private string $urlZipGeocoding = 'https://api.openweathermap.org/geo/1.0/zip';

    private string $urlReverseGeocoding = 'https://api.openweathermap.org/geo/1.0/reverse';

    /**
     * @return Location[]
     * @throws Exception
     */
    public function getCoordinatesByLocationName(string $locationName, int $limit = 5): array
    {
        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlDirectGeocoding,
            query: [
                'q' => $locationName,
                'limit' => $limit
            ]
        );

        return $this->createEntityList($data, Location::class);
    }

    /**
     * @throws Exception
     */
    public function getCoordinatesByZipCode(string $zipCode, string $countryCode): ZipLocation
    {
        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlZipGeocoding,
            query: [
                'zip' => \sprintf('%s,%s', $zipCode, $countryCode)
            ]
        );

        return new ZipLocation($data);
    }

    /**
     * @return Location[]
     * @throws Exception
     */
    public function getLocationNameByCoordinates(float $latitude, float $longitude, int $limit = 5): array
    {
        $this->validateCoordinate($latitude, $longitude);

        $data = $this->sendRequest(
            method: 'GET',
            baseUrl: $this->urlReverseGeocoding,
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'limit' => $limit
            ]
        );

        return $this->createEntityList($data, Location::class);
    }
}