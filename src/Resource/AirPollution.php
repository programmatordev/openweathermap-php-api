<?php

namespace ProgrammatorDev\OpenWeatherMap\Resource;

use ProgrammatorDev\Api\Resource;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Current;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\History;
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

    public function forecast(float $latitude, float $longitude): Forecast
    {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);

        // https://openweathermap.org/api/air-pollution
        /** @var Forecast $forecast */
        $forecast = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
            ])
            ->get('/data/2.5/air_pollution/forecast')
            ->entity(Forecast::class);

        return $forecast;
    }

    public function history(
        float $latitude,
        float $longitude,
        \DateTimeInterface $startAt,
        \DateTimeInterface $endAt,
    ): History {
        $latitude = Assert::latitude($latitude);
        $longitude = Assert::longitude($longitude);
        Assert::chronologicalRange($startAt, $endAt);

        // A non-future end also constrains the ordered start.
        // The documented minimum is left to OpenWeather because live availability differs.
        $endAt = Assert::notFuture($endAt, 'end date');

        // https://openweathermap.org/api/air-pollution
        /** @var History $history */
        $history = $this
            ->endpoint()
            ->queries([
                'lat' => $latitude,
                'lon' => $longitude,
                'start' => $startAt->getTimestamp(),
                'end' => $endAt->getTimestamp(),
            ])
            ->get('/data/2.5/air_pollution/history')
            ->entity(History::class);

        return $history;
    }
}
