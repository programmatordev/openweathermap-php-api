<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

trait CoordinateValidatorTrait
{
    use BetweenValidatorTrait;

    private function validateCoordinate(float $latitude, float $longitude): void
    {
        $this->validateBetween('latitude', $latitude, -90, 90);
        $this->validateBetween('longitude', $longitude, -180, 180);
    }
}