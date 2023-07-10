<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

trait ChoiceValidatorTrait
{
    /**
     * @throws ValidationException
     */
    private function validateChoice(string $name, mixed $value, array $options): void
    {
        if ( ! in_array($value, $options)) {
            throw new ValidationException(
                \sprintf(
                    'The "%s" value "%s" is invalid. Accepted values are: "%s".',
                    $name,
                    $value,
                    implode('", "', $options)
                )
            );
        }
    }
}