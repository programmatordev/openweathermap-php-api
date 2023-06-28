<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidNumResultsException;

trait ValidateNumResultsTrait
{
    /**
     * @throws InvalidNumResultsException
     */
    private function validateNumResults(?int $numResults): void
    {
        if (is_null($numResults)) {
            return;
        }

        if ($numResults <= 0) {
            throw new InvalidNumResultsException(
                \sprintf('The number of results "%s" is invalid. Must be greater or equal to 1.', $numResults)
            );
        }
    }
}