<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateBlankTrait
{
    private function validateBlank(string $name, string $value): void
    {
        if (empty($value)) {
            throw new \UnexpectedValueException(
                \sprintf('The "%s" value should not be blank.', $name)
            );
        }
    }
}