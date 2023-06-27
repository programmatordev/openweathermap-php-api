<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\OpenWeatherMap\Entity\Location;

class CurrentWeather extends Weather
{
    private Location $location;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->location = new Location([
            'id' => !empty($data['id']) ? $data['id'] : null,
            'name' => !empty($data['name']) ? $data['name'] : null,
            'country' => $data['sys']['country'] ?? null,
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