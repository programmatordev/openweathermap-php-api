<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidChoiceException;
use ProgrammatorDev\OpenWeatherMap\MeasurementSystem;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateChoiceTrait;

trait WithMeasurementSystemTrait
{
    use ValidateChoiceTrait;

    /**
     * @throws InvalidChoiceException
     */
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