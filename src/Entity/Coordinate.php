<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Coordinate
{
    private float $latitude;

    private float $longitude;

    public function __construct(array $data)
    {
        $this->latitude = $data['lat'];
        $this->longitude = $data['lon'];
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }
}