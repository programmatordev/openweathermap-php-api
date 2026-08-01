<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Current;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithLanguage;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithUnits;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Weather extends Resource
{
    use WithLanguage;
    use WithUnits;

    public function current(float $latitude, float $longitude): Current
    {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // https://openweathermap.org/api/current?collection=current_forecast
        /** @var Current $weather */
        $weather = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/2.5/weather')
            ->entity(Current::class);

        return $weather;
    }
}
