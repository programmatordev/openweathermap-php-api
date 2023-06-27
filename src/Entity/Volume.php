<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Volume
{
    private ?float $lastOneHour;

    private ?float $lastThreeHours;

    public function __construct(array $data)
    {
        $this->lastOneHour = $data['1h'] ?? null;
        $this->lastThreeHours = $data['3h'] ?? null;
    }

    /**
     * Volume for the last 1 hour, in millimetres (mm)
     */
    public function getLastOneHour(): ?float
    {
        return $this->lastOneHour;
    }

    /**
     * Volume for the last 3 hours, in millimetres (mm)
     */
    public function getLastThreeHours(): ?float
    {
        return $this->lastThreeHours;
    }
}