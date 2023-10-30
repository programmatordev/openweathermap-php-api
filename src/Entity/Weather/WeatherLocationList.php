<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\OpenWeatherMap\Entity\Location;
use ProgrammatorDev\OpenWeatherMap\Util\EntityListTrait;

class WeatherLocationList
{
    use EntityListTrait;

    private int $numResults;

    private Location $location;

    /** @var Weather[] */
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
        $this->list = $this->createEntityList(Weather::class, $data['list']);
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getList(): array
    {
        return $this->list;
    }
}