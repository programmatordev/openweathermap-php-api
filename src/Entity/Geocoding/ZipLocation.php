<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Geocoding;

use ProgrammatorDev\OpenWeatherMap\Entity\Coordinate;

class ZipLocation
{
    private string $zipCode;

    private string $name;

    private Coordinate $coordinate;

    private string $countryCode;

    public function __construct(array $data)
    {
        $this->zipCode = $data['zip'];
        $this->name = $data['name'];
        $this->countryCode = $data['country'];
        $this->coordinate = new Coordinate([
            'lat' => $data['lat'],
            'lon' => $data['lon']
        ]);
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}