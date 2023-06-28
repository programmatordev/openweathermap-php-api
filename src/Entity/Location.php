<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Location
{
    private ?int $id;

    private ?string $name;

    private ?string $state;

    private ?string $countryCode;

    private ?array $localNames;

    private ?int $population;

    private Coordinate $coordinate;

    private ?Timezone $timezone;

    private ?\DateTimeImmutable $sunriseAt;

    private ?\DateTimeImmutable $sunsetAt;

    public function __construct(array $data)
    {
        $this->coordinate = new Coordinate([
            'lat' => $data['lat'],
            'lon' => $data['lon']
        ]);

        $this->id = !empty($data['id']) ? $data['id'] : null;
        $this->name = !empty($data['name']) ? $data['name'] : null;
        $this->state = !empty($data['state']) ? $data['state'] : null;
        $this->countryCode = !empty($data['country']) ? $data['country'] : null;
        $this->localNames = !empty($data['local_names']) ? $data['local_names'] : null;
        $this->population = !empty($data['population']) ? $data['population'] : null;

        $this->sunriseAt = !empty($data['sunrise'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunrise'], new \DateTimeZone('UTC'))
            : null;
        $this->sunsetAt = !empty($data['sunset'])
            ? \DateTimeImmutable::createFromFormat('U', $data['sunset'], new \DateTimeZone('UTC'))
            : null;

        $this->timezone = isset($data['timezone_offset'])
            ? new Timezone(['timezone_offset' => $data['timezone_offset']])
            : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getLocalNames(): ?array
    {
        return $this->localNames;
    }

    public function getLocalName(string $countryCode): ?string
    {
        $countryCode = strtolower($countryCode);
        return $this->localNames[$countryCode] ?? null;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function getCoordinate(): Coordinate
    {
        return $this->coordinate;
    }

    public function getTimezone(): ?Timezone
    {
        return $this->timezone;
    }

    public function getSunriseAt(): ?\DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    public function getSunsetAt(): ?\DateTimeImmutable
    {
        return $this->sunsetAt;
    }
}