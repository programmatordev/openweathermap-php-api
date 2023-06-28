<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

class AtmosphericPressure
{
    private int $pressure;

    private ?int $seaLevelPressure;

    private ?int $groundLevelPressure;

    public function __construct(array $data)
    {
        $this->pressure = $data['pressure'];

        $this->seaLevelPressure = $data['sea_level'] ?? null;
        $this->groundLevelPressure = $data['grnd_level'] ?? null;
    }

    public function getPressure(): int
    {
        return $this->pressure;
    }

    public function getSeaLevelPressure(): ?int
    {
        return $this->seaLevelPressure;
    }

    public function getGroundLevelPressure(): ?int
    {
        return $this->groundLevelPressure;
    }
}