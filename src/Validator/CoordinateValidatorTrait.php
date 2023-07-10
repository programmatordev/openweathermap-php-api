<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

trait CoordinateValidatorTrait
{
    use BetweenValidatorTrait;

    /**
     * @throws ValidationException
     */
    private function validateCoordinate(float $latitude, float $longitude): void
    {
        $this->validateBetween('latitude', $latitude, -90, 90);
        $this->validateBetween('longitude', $longitude, -180, 180);
    }
}