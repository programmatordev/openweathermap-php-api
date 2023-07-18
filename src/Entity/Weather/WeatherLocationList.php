<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Util\CreateEntityListTrait;

class WeatherLocationList
{
    use CreateEntityListTrait;

    private int $numResults;

    private Location $location;

    private array $list;

    public function __construct(array $data)
    {
        $this->numResults = $data['cnt'];

        $this->location = new Location([
            'id' => $data['city']['id'],
            'name' => $data['city']['name'],
            'country' => $data['city']['country'],
            'population' => $data['city']['population'],
            'lat' => $data['city']['coord']['lat'],
            'lon' => $data['city']['coord']['lon'],
            'sunrise' => $data['city']['sunrise'],
            'sunset' => $data['city']['sunset'],
            'timezone_offset' => $data['city']['timezone']
        ]);

        $this->list = $this->createEntityList($data['list'], Weather::class);
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @return Weather[]
     */
    public function getList(): array
    {
        return $this->list;
    }
}