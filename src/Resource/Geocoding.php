<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\PostalLocation;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Geocoding extends Resource
{
    /**
     * @return list<Location>
     */
    public function byName(string $name, ?int $limit = null): array
    {
        $name = Assert::notBlank($name, 'location name');

        // The direct endpoint documents a maximum of five results.
        // https://openweathermap.org/api/geocoding-api?collection=other
        if ($limit !== null) {
            $limit = Assert::integerBetween($limit, 1, 5, 'result limit');
        }

        return $this
            ->endpoint()
            ->queries([
                'q' => $name,
                'limit' => $limit,
            ])
            ->get('/geo/1.0/direct')
            ->collection(Location::class);
    }

    public function byPostalCode(string $postalCode, string $countryCode): PostalLocation
    {
        $postalCode = Assert::notBlank($postalCode, 'postal code');

        $countryCode = strtoupper(trim($countryCode));

        if (preg_match('/^[A-Z]{2}$/D', $countryCode) !== 1) {
            throw new \InvalidArgumentException(
                'The country code must contain exactly two ASCII letters.',
            );
        }

        /** @var PostalLocation $location */
        $location = $this
            ->endpoint()
            ->query('zip', sprintf('%s,%s', $postalCode, $countryCode))
            ->get('/geo/1.0/zip')
            ->entity(PostalLocation::class);

        return $location;
    }

    /**
     * @return list<Location>
     */
    public function byCoordinates(
        float $latitude,
        float $longitude,
        ?int $limit = null,
    ): array {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // The reverse endpoint documents no maximum result limit.
        // https://openweathermap.org/api/geocoding-api?collection=other
        if ($limit !== null) {
            $limit = Assert::positiveInteger($limit, 'result limit');
        }

        return $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'limit' => $limit,
            ])
            ->get('/geo/1.0/reverse')
            ->collection(Location::class);
    }
}
