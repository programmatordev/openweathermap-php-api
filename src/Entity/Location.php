<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Location
{
    private string $name;

    private array $localNames;

    private ?string $localFeatureName;

    private ?string $localAsciiName;

    private Coordinates $coordinates;

    private string $countryCode;

    private ?string $state;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->countryCode = $data['country'];
        $this->coordinates = new Coordinates([
            'lat' => $data['lat'],
            'lon' => $data['lon']
        ]);

        // Optional data
        $this->localNames = $data['local_names'] ?? [];
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

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getState(): ?string
    {
        return $this->state;
    }
}