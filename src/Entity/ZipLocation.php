<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class ZipLocation
{
    private string $zip;

    private string $name;

    private Coordinate $coordinate;

    private string $countryCode;

    public function __construct(array $data)
    {
        $this->zip = $data['zip'];
        $this->name = $data['name'];
        $this->countryCode = $data['country'];
        $this->coordinate = new Coordinate([
            'lat' => $data['lat'],
            'lon' => $data['lon']
        ]);
    }

    public function getZip(): string
    {
        return $this->zip;
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