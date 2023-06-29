<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidApplicationKeyException;

trait ValidateApplicationKeyTrait
{
    /**
     * @throws InvalidApplicationKeyException
     */
    private function validateApplicationKey(string $applicationKey): void
    {
        if (empty($applicationKey)) {
            throw new InvalidApplicationKeyException('The application key value should not be empty.');
        }
    }
}