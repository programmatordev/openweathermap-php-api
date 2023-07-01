<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

class MoonPhase
{
    public const NEW_MOON = 'NEW_MOON';
    public const WAXING_CRESCENT = 'WAXING_CRESCENT';
    public const FIRST_QUARTER_MOON = 'FIRST_QUARTER_MOON';
    public const WAXING_GIBBOUS = 'WAXING_GIBBOUS';
    public const FULL_MOON = 'FULL_MOON';
    public const WANING_GIBBOUS = 'WANING_GIBBOUS';
    public const LAST_QUARTER_MOON = 'LAST_QUARTER_MOON';
    public const WANING_CRESCENT = 'WANING_CRESCENT';

    private float $value;

    private string $name;

    private string $sysName;

    public function __construct(array $data)
    {
        $this->value = $data['moon_phase'];

        $this->setNames($this->value);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSysName(): string
    {
        return $this->sysName;
    }

    private function setNames(float $value): void
    {
        $this->sysName = match (true) {
            $value > 0 && $value < 0.25 => self::WAXING_CRESCENT,
            $value === 0.25 => self::FIRST_QUARTER_MOON,
            $value > 0.25 && $value < 0.5 => self::WAXING_GIBBOUS,
            $value === 0.5 => self::FULL_MOON,
            $value > 0.5 && $value < 0.75 => self::WANING_GIBBOUS,
            $value === 0.75 => self::LAST_QUARTER_MOON,
            $value > 0.75 && $value < 1 => self::WANING_CRESCENT,
            default => self::NEW_MOON // 0.0 or 1.0
        };

        // Convert string to user-friendly text
        $this->name = ucfirst(strtolower(str_replace('_', ' ', $this->sysName)));
    }
}