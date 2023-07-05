<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidChoiceException;

trait ValidateChoiceTrait
{
    private function validateChoice(string $name, mixed $value, array $options): void
    {
        if ( ! in_array($value, $options)) {
            throw new InvalidChoiceException(
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