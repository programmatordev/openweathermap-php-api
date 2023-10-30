<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Util;

trait AirQualityQualitativeNameTrait
{
    // Levels based on https://openweathermap.org/api/air-pollution
    private array $airQualityIndex = [
        0 => 'Undefined',
        1 => 'Good',
        2 => 'Fair',
        3 => 'Moderate',
        4 => 'Poor',
        5 => 'Very poor'
    ];

    private function getAirQualityQualitativeName(int $index): string
    {
        return $this->airQualityIndex[$index];
    }
}