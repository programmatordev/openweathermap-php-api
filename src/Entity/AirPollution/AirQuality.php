<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Util\AirQualityQualitativeNameTrait;

class AirQuality
{
    use AirQualityQualitativeNameTrait;

    private int $index;

    private string $qualitativeName;

    public function __construct(array $data)
    {
        $this->index = $data['aqi'];
        $this->qualitativeName = $this->getAirQualityQualitativeName($this->index);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getQualitativeName(): string
    {
        return $this->qualitativeName;
    }
}