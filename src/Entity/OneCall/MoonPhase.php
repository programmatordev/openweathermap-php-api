<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class MoonPhase
{
    private float $phaseValue;

    private string $phaseName;

    public function __construct(float $phaseValue)
    {
        $this->phaseValue = $phaseValue;
        $this->phaseName = $this->findPhaseName($phaseValue);
    }

    public function getPhaseValue(): float
    {
        return $this->phaseValue;
    }

    public function getPhaseName(): string
    {
        return $this->phaseName;
    }

    private function findPhaseName(float $phaseValue): string
    {
        return match (true) {
            $phaseValue > 0 && $phaseValue < 0.25 => 'Waxing crescent',
            $phaseValue === 0.25 => 'First quarter moon',
            $phaseValue > 0.25 && $phaseValue < 0.5 => 'Waxing gibbous',
            $phaseValue === 0.5 => 'Full moon',
            $phaseValue > 0.5 && $phaseValue < 0.75 => 'Waning gibbous',
            $phaseValue === 0.75 => 'Last quarter moon',
            $phaseValue > 0.75 && $phaseValue < 1 => 'Waning crescent',
            default => 'New moon' // between 0.75 and 1.0
        };
    }
}