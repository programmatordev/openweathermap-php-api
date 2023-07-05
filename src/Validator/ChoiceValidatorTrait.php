<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

trait ChoiceValidatorTrait
{
    private function validateChoice(string $name, mixed $value, array $options): void
    {
        if ( ! in_array($value, $options)) {
            throw new \UnexpectedValueException(
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