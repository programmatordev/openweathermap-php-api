<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\PostalLocation;
use ProgrammatorDev\OpenWeatherMap\Value\Coordinates;

final class Geocoding extends Resource
{
    /**
     * @return list<Location>
     */
    public function byName(string $name, ?int $limit = null): array
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException('The location name must be a non-empty string.');
        }

        // The direct endpoint documents a maximum of five results.
        // https://openweathermap.org/api/geocoding-api?collection=other
        if ($limit !== null && ($limit < 1 || $limit > 5)) {
            throw new \InvalidArgumentException('The result limit must be between 1 and 5.');
        }

        $query = ['q' => $name];

        if ($limit !== null) {
            $query['limit'] = $limit;
        }

        return $this
            ->endpoint()
            ->queries($query)
            ->get('/geo/1.0/direct')
            ->collection(Location::class);
    }

    public function byPostalCode(string $postalCode, string $countryCode): PostalLocation
    {
        $postalCode = trim($postalCode);

        if ($postalCode === '') {
            throw new \InvalidArgumentException('The postal code must be a non-empty string.');
        }

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
        $coordinates = Coordinates::from($latitude, $longitude);

        // The reverse endpoint documents no maximum result limit.
        // https://openweathermap.org/api/geocoding-api?collection=other
        if ($limit !== null && $limit < 1) {
            throw new \InvalidArgumentException('The result limit must be at least 1.');
        }

        $query = [
            'lat' => $coordinates->latitude(),
            'lon' => $coordinates->longitude(),
        ];

        if ($limit !== null) {
            $query['limit'] = $limit;
        }

        return $this
            ->endpoint()
            ->queries($query)
            ->get('/geo/1.0/reverse')
            ->collection(Location::class);
    }
}
