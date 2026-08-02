<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Current;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class AirPollution extends Resource
{
    public function current(float $latitude, float $longitude): Current
    {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // https://openweathermap.org/api/air-pollution
        /** @var Current $current */
        $current = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
            ])
            ->get('/data/2.5/air_pollution')
            ->entity(Current::class);

        return $current;
    }
}
