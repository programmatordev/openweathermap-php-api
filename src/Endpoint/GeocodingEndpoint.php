<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipCodeLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Exception\BadRequestException;
use ProgrammatorDev\OpenWeatherMap\Exception\NotFoundException;
use ProgrammatorDev\OpenWeatherMap\Exception\TooManyRequestsException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnauthorizedException;
use ProgrammatorDev\OpenWeatherMap\Exception\UnexpectedErrorException;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\CoordinateValidatorTrait;
use ProgrammatorDev\OpenWeatherMap\Validator\GreaterThanValidatorTrait;

class GeocodingEndpoint extends AbstractEndpoint
{
    use CreateEntityListTrait;
    use CoordinateValidatorTrait;
    use GreaterThanValidatorTrait;

    private const NUM_RESULTS = 5;

    private string $urlGeocodingDirect = 'https://api.openweathermap.org/geo/1.0/direct';

    private string $urlGeocodingZipCode = 'https://api.openweathermap.org/geo/1.0/zip';

    private string $urlGeocodingReverse = 'https://api.openweathermap.org/geo/1.0/reverse';

    /**
     * @return Location[]
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getCoordinatesByLocationName(string $locationName, int $numResults = self::NUM_RESULTS): array
    {
        $this->validateGreaterThan('numResults', $numResults, 0);

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
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
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
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     */
    public function getLocationNameByCoordinates(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): array
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateGreaterThan('numResults', $numResults, 0);

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