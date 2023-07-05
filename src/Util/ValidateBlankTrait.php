<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidBlankException;

trait ValidateBlankTrait
{
    /**
     * @throws InvalidBlankException
     */
    private function validateBlank(string $name, string $value): void
    {
        if (empty($value)) {
            throw new InvalidBlankException(
                \sprintf('The "%s" value should not be blank.', $name)
            );
        }
    }
}