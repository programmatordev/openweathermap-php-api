<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint;

use Http\Client\Exception;
use ProgrammatorDev\OpenWeatherMap\Endpoint\Util\ValidationTrait;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\ZipCodeLocation;
use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Exception\ApiErrorException;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;
use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

class GeocodingEndpoint extends AbstractEndpoint
{
    use ValidationTrait;
    use CreateEntityListTrait;

    private const NUM_RESULTS = 5;

    protected int $cacheTtl = 60 * 60 * 24 * 30; // 30 days

    /**
     * @return Location[]
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getByLocationName(string $locationName, int $numResults = self::NUM_RESULTS): array
    {
        $this->validateSearchQuery($locationName, 'locationName');
        $this->validateNumResults($numResults);

        $data = $this->sendRequest(
            method: 'GET',
            path: '/geo/1.0/direct',
            query: [
                'q' => $locationName,
                'limit' => $numResults
            ]
        );

        return $this->createEntityList($data, Location::class);
    }

    /**
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getByZipCode(string $zipCode, string $countryCode): ZipCodeLocation
    {
        $this->validateSearchQuery($zipCode, 'zipCode');
        $this->validateCountryCode($countryCode);

        $data = $this->sendRequest(
            method: 'GET',
            path: '/geo/1.0/zip',
            query: [
                'zip' => \sprintf('%s,%s', $zipCode, $countryCode)
            ]
        );

        return new ZipCodeLocation($data);
    }

    /**
     * @return Location[]
     * @throws Exception
     * @throws ApiErrorException
     * @throws ValidationException
     */
    public function getByCoordinate(float $latitude, float $longitude, int $numResults = self::NUM_RESULTS): array
    {
        $this->validateCoordinate($latitude, $longitude);
        $this->validateNumResults($numResults);

        $data = $this->sendRequest(
            method: 'GET',
            path: '/geo/1.0/reverse',
            query: [
                'lat' => $latitude,
                'lon' => $longitude,
                'limit' => $numResults
            ]
        );

        return $this->createEntityList($data, Location::class);
    }
}