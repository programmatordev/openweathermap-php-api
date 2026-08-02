<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Concern;

use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Components;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;

trait HasAirQuality
{
    public function airQualityIndex(): ?AirQualityIndex
    {
        return $this->airQuality->airQualityIndex();
    }

    public function components(): ?Components
    {
        return $this->airQuality->components();
    }
}
