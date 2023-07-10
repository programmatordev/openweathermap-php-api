<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\MeasurementSystem\MeasurementSystem;
use ProgrammatorDev\OpenWeatherMap\Validator\ChoiceValidatorTrait;

trait WithMeasurementSystemTrait
{
    use ChoiceValidatorTrait;

    public function withMeasurementSystem(string $measurementSystem): static
    {
        $this->validateChoice('measurementSystem', $measurementSystem, MeasurementSystem::getList());

        $clone = clone $this;
        $clone->measurementSystem = $measurementSystem;

        return $clone;
    }

    public function getMeasurementSystem(): string
    {
        return $this->measurementSystem;
    }
}