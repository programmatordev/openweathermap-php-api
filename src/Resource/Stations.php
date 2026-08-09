<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\Station;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Stations extends Resource
{
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
