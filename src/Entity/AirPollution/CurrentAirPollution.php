<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\Location;

class CurrentAirPollution extends AirPollution
{
    private Location $location;

    public function __construct(array $data)
    {
        parent::__construct($data['list'][0]);

        $this->location = new Location([
            'lat' => $data['coord']['lat'],
            'lon' => $data['coord']['lon']
        ]);
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}