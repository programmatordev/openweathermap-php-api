<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidMeasurementSystemException;
use ProgrammatorDev\OpenWeatherMap\Util\ValidateMeasurementSystemTrait;

trait WithMeasurementSystemTrait
{
    use ValidateMeasurementSystemTrait;

    /**
     * @throws InvalidMeasurementSystemException
     */
    public function withMeasurementSystem(string $measurementSystem): static
    {
        $this->validateMeasureSystem($measurementSystem);

        $clone = clone $this;
        $clone->measurementSystem = $measurementSystem;

        return $clone;
    }
}