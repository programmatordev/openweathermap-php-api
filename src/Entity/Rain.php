<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Rain extends Volume
{
    private ?int $precipitationProbability;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->precipitationProbability = isset($data['pop'])
            ? round($data['pop'] * 100)
            : null;
    }

    /**
     * Probability of precipitation, in percentage (%)
     */
    public function getPrecipitationProbability(): ?int
    {
        return $this->precipitationProbability;
    }
}