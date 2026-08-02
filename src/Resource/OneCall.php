<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Current;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithLanguage;
use ProgrammatorDev\OpenWeatherMap\Resource\Concern\WithUnits;
use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class OneCall extends Resource
{
    use WithLanguage;
    use WithUnits;

    public function current(float $latitude, float $longitude): Current
    {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // https://openweathermap.org/api/one-call-4#current
        /** @var Current $current */
        $current = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'units' => $this->resolvedUnits(),
                'lang' => $this->resolvedLanguage(),
            ])
            ->get('/data/4.0/onecall/current')
            ->entity(Current::class);

        return $current;
    }
}
