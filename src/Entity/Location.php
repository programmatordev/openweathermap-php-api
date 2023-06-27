<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Location
{
    private string $name;

    private string $countryCode;

    private Coordinate $coordinate;

    private ?array $localNames;

    private ?string $localFeatureName;

    private ?string $localAsciiName;

    private ?string $state;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->countryCode = $data['country'];
        $this->coordinate = new Coordinate([
            'lat' => $data['lat'],
            'lon' => $data['lon']
        ]);

        // Optional data
        $this->localNames = $data['local_names'] ?? null;
        $this->localFeatureName = $this->localNames['feature_name'] ?? null;
        $this->localAsciiName = $this->localNames['ascii'] ?? null;
        $this->state = $data['state'] ?? null;

        unset($this->localNames['feature_name']);
        unset($this->localNames['ascii']);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getLocalNames(): array
    {
        return $this->localNames;
    }

    public function getLocalName(string $countryCode): ?string
    {
        $countryCode = strtolower($countryCode);
        return $this->localNames[$countryCode] ?? null;
    }

    public function getLocalFeatureName(): ?string
    {
        return $this->localFeatureName;
    }

    public function getLocalAsciiName(): ?string
    {
        return $this->localAsciiName;
    }

    public function getState(): ?string
    {
        return $this->state;
    }
}