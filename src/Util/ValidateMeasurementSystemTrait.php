<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidMeasurementSystemException;
use ProgrammatorDev\OpenWeatherMap\MeasurementSystem;

trait ValidateMeasurementSystemTrait
{
    /**
     * @throws InvalidMeasurementSystemException
     */
    private function validateMeasureSystem(string $measurementSystem): void
    {
        $options = MeasurementSystem::getList();

        if ( ! in_array($measurementSystem, $options)) {
            throw new InvalidMeasurementSystemException(
                \sprintf(
                    'The value "%s" is invalid. Accepted values are: "%s".',
                    $measurementSystem,
                    implode('", "', $options)
                )
            );
        }
    }
}