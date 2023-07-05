<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidCoordinateException;

trait ValidateCoordinateTrait
{
    use ValidateBetweenTrait;

    /**
     * @throws InvalidCoordinateException
     */
    private function validateCoordinate(float $latitude, float $longitude): void
    {
        try {
            $this->validateBetween('latitude', $latitude, -90, 90);
            $this->validateBetween('longitude', $longitude, -180, 180);
        }
        catch (\UnexpectedValueException $error) {
            throw new InvalidCoordinateException($error->getMessage());
        }
    }
}