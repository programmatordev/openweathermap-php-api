<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\Station;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Stations extends Resource
{
    public function create(
        string $externalId,
        string $name,
        float $latitude,
        float $longitude,
        float $altitude,
    ): Station {
        $externalId = Assert::notBlank($externalId, 'external station ID');
        $name = Assert::notBlank($name, 'station name');
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);
        $altitude = Assert::finiteNumber($altitude, 'station altitude');

        // https://openweathermap.org/api/stations
        /** @var Station $station */
        $station = $this
            ->endpoint()
            ->json([
                'external_id' => $externalId,
                'name' => $name,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'altitude' => $altitude,
            ])
            ->post('/data/3.0/stations')
            ->entity(Station::class);

        return $station;
    }

    /**
     * @return list<Station>
     */
    public function all(): array
    {
        // https://openweathermap.org/api/stations
        return $this
            ->endpoint()
            ->get('/data/3.0/stations')
            ->collection(Station::class);
    }

    public function find(string $id): Station
    {
        $id = Assert::notBlank($id, 'station ID');

        // https://openweathermap.org/api/stations
        /** @var Station $station */
        $station = $this
            ->endpoint()
            ->get('/data/3.0/stations/{id}', [
                'id' => $id,
            ])
            ->entity(Station::class);

        return $station;
    }
}
