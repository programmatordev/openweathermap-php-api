<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

class AirQuality
{
    private int $index;

    private string $qualitativeName;

    public function __construct(array $data)
    {
        $this->index = $data['aqi'];
        $this->qualitativeName = $this->findQualitativeName($this->index);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getQualitativeName(): string
    {
        return $this->qualitativeName;
    }

    private function findQualitativeName(int $index): string
    {
        // levels based on https://openweathermap.org/api/air-pollution
        return match ($index) {
            1 => 'Good',
            2 => 'Fair',
            3 => 'Moderate',
            4 => 'Poor',
            5 => 'Very Poor',
            default => 'Undefined'
        };
    }
}