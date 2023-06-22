<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\ZipLocation;
use ProgrammatorDev\OpenWeatherMap\HttpClient\ResponseMediator;
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
        $url = $this->createUrl($this->urlDirectGeocoding, [
            'q' => $locationName,
            'limit' => $limit
        ]);

        $data = ResponseMediator::toArray(
            $this->getHttpClient()->get($url)
        );

        return $this->createEntityList($data, Location::class);
    }

    /**
     * @throws Exception
     */
    public function getCoordinatesByZipCode(string $zipCode, string $countryCode, int $limit = 5): ZipLocation
    {
        $url = $this->createUrl($this->urlZipGeocoding, [
            'zip' => \sprintf('%s,%s', $zipCode, $countryCode),
            'limit' => $limit
        ]);

        $data = ResponseMediator::toArray(
            $this->getHttpClient()->get($url)
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

        $url = $this->createUrl($this->urlReverseGeocoding, [
            'lat' => $latitude,
            'lon' => $longitude,
            'limit' => $limit
        ]);

        $data = ResponseMediator::toArray(
            $this->getHttpClient()->get($url)
        );

        return $this->createEntityList($data, Location::class);
    }
}