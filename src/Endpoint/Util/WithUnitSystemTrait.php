<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\UnitSystem\UnitSystem;
use ProgrammatorDev\OpenWeatherMap\Validator\ChoiceValidatorTrait;

trait WithUnitSystemTrait
{
    use ChoiceValidatorTrait;

    public function withUnitSystem(string $unitSystem): static
    {
        $this->validateChoice('unitSystem', $unitSystem, UnitSystem::getList());

        $clone = clone $this;
        $clone->unitSystem = $unitSystem;

        return $clone;
    }

    public function getUnitSystem(): string
    {
        return $this->unitSystem;
    }
}