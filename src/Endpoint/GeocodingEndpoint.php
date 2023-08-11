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
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class GeocodingEndpoint extends AbstractEndpoint
{
    use CreateEntityListTrait;

    private const NUM_RESULTS = 5;

    private string $urlGeocodingDirect = 'https://api.openweathermap.org/geo/1.0/direct';

    private string $urlGeocodingZipCode = 'https://api.openweathermap.org/geo/1.0/zip';

    private string $urlGeocodingReverse = 'https://api.openweathermap.org/geo/1.0/reverse';

    protected ?int $cacheTtl = 60 * 60 * 24 * 30; // 30 days

    /**
     * @return Location[]
     * @throws Exception
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws TooManyRequestsException
     * @throws UnauthorizedException
     * @throws UnexpectedErrorException
     * @throws ValidationException
     */
    public function getByLocationName(string $locationName, int $numResults = self::NUM_RESULTS): array
    {
        Validator::notBlank()->assert($locationName, 'locationName');
        Validator::greaterThan(0)->assert($numResults, 'numResults');

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
     * @throws ValidationException
     */
    public function getByZipCode(string $zipCode, string $countryCode): ZipCodeLocation
    {
        Validator::notBlank()->assert($zipCode, 'zipCode');
        Validator::notBlank()->assert($countryCode, 'countryCode');

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
     * @throws ValidationException
     */
    public function getByCoordinate(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): array
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
        Validator::greaterThan(0)->assert($numResults, 'numResults');

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