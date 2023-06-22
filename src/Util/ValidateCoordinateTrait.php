<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateCoordinateTrait
{
    private function validateCoordinate(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException(
                \sprintf('The latitude "%f" is invalid. Must be between -90 and 90.', $latitude)
            );
        }

        if ($longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException(
                \sprintf('The longitude "%f" is invalid. Must be between -180 and 180.', $longitude)
            );
        }
    }
}