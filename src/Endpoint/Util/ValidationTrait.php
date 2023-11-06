<?php

namespace ProgrammatorDev\OpenWeatherMap\Endpoint\Util;

use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;
use ProgrammatorDev\YetAnotherPhpValidator\Validator;

trait ValidationTrait
{
    /**
     * @throws ValidationException
     */
    private function validateCoordinate(float $latitude, float $longitude): void
    {
        Validator::range(-90, 90)->assert($latitude, 'latitude');
        Validator::range(-180, 180)->assert($longitude, 'longitude');
    }

    /**
     * @throws ValidationException
     */
    private function validateDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): void
    {
        $nowDate = new \DateTime('now');
        $nowDate->setTime($nowDate->format('H'), 0);

        // Start date must be less or equal to end date
        Validator::lessThanOrEqual(
            constraint: $endDate,
            message: 'The startDate value should be less than or equal to the endDate.'
        )->assert($startDate);

        // End date must be less or equal to today date
        Validator::lessThanOrEqual($nowDate)->assert($endDate, 'endDate');
    }

    /**
     * @throws ValidationException
     */
    private function validatePastDate(\DateTimeInterface $date, string $name): void
    {
        Validator::lessThan(new \DateTime('now'))->assert($date, $name);
    }

    /**
     * @throws ValidationException
     */
    private function validateSearchQuery(string $searchQuery, string $name): void
    {
        Validator::notBlank()->assert($searchQuery, $name);
    }

    /**
     * @throws ValidationException
     */
    private function validateNumResults(int $numResults): void
    {
        Validator::greaterThan(0)->assert($numResults, 'numResults');
    }

    /**
     * @throws ValidationException
     */
    private function validateCountryCode(string $countryCode): void
    {
        Validator::country()->assert($countryCode, 'countryCode');
    }
}