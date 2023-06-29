<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;

trait ValidateCoordinateTrait
{
    /**
     * @throws InvalidCoordinateException
     */
    private function validateCoordinate(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new InvalidCoordinateException(
                \sprintf('The latitude "%f" is invalid. Must be between -90 and 90.', $latitude)
            );
        }

        if ($longitude < -180 || $longitude > 180) {
            throw new InvalidCoordinateException(
                \sprintf('The longitude "%f" is invalid. Must be between -180 and 180.', $longitude)
            );
        }
    }
}