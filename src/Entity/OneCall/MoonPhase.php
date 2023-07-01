<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class MoonPhase
{
    private float $value;

    private string $name;

    public function __construct(array $data)
    {
        $this->value = $data['moon_phase'];
        $this->name = $this->findName($this->value);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function findName(float $value): string
    {
        return match (true) {
            $value > 0 && $value < 0.25 => 'Waxing crescent',
            $value === 0.25 => 'First quarter moon',
            $value > 0.25 && $value < 0.5 => 'Waxing gibbous',
            $value === 0.5 => 'Full moon',
            $value > 0.5 && $value < 0.75 => 'Waning gibbous',
            $value === 0.75 => 'Last quarter moon',
            $value > 0.75 && $value < 1 => 'Waning crescent',
            default => 'New moon' // 0.0 or 1.0
        };
    }
}