<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Geocoding\Location;

final class Geocoding extends Resource
{
    /**
     * @return list<Location>
     */
    public function byName(string $name, ?int $limit = null): array
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('The location name must be a non-empty string.');
        }

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
}
