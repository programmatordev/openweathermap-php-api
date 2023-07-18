<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\OpenWeatherMap\Entity\Location;

class WeatherLocation extends Weather
{
    private Location $location;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->location = new Location([
            'id' => $data['id'],
            'name' => $data['name'],
            'country' => $data['sys']['country'],
            'lat' => $data['coord']['lat'],
            'lon' => $data['coord']['lon'],
            'sunrise' => $data['sys']['sunrise'],
            'sunset' => $data['sys']['sunset'],
            'timezone_offset' => $data['timezone']
        ]);
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}