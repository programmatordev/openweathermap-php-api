<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

trait BlankValidatorTrait
{
    /**
     * @throws ValidationException
     */
    private function validateBlank(string $name, string $value): void
    {
        if (empty($value)) {
            throw new ValidationException(
                \sprintf('The "%s" value should not be blank.', $name)
            );
        }
    }
}