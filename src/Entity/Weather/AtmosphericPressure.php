<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

class AtmosphericPressure
{
    private int $pressure;

    private ?int $seaLevel;

    private ?int $groundLevel;

    public function __construct(array $data)
    {
        $this->pressure = $data['pressure'];

        $this->seaLevel = $data['sea_level'] ?? null;
        $this->groundLevel = $data['grnd_level'] ?? null;
    }

    public function getPressure(): int
    {
        return $this->pressure;
    }

    public function getSeaLevel(): ?int
    {
        return $this->seaLevel;
    }

    public function getGroundLevel(): ?int
    {
        return $this->groundLevel;
    }
}