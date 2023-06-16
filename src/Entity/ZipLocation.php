<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class ZipLocation
{
    private string $zip;

    private string $name;

    private Coordinates $coordinates;

    private string $countryCode;

    public function __construct(array $data)
    {
        $this->zip = $data['zip'];
        $this->name = $data['name'];
        $this->countryCode = $data['country'];
        $this->coordinates = new Coordinates([
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

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}