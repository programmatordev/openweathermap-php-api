<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Volume
{
    private ?float $lastOneHourVolume;

    private ?float $lastThreeHoursVolume;

    public function __construct(array $data)
    {
        $this->lastOneHourVolume = $data['1h'] ?? null;
        $this->lastThreeHoursVolume = $data['3h'] ?? null;
    }

    /**
     * Volume for the last 1 hour, in millimetres (mm)
     */
    public function getLastOneHourVolume(): ?float
    {
        return $this->lastOneHourVolume;
    }

    /**
     * Volume for the last 3 hours, in millimetres (mm)
     */
    public function getLastThreeHoursVolume(): ?float
    {
        return $this->lastThreeHoursVolume;
    }
}