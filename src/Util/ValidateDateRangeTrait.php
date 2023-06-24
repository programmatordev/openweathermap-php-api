<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

use ProgrammatorDev\OpenWeatherMap\Exception\InvalidDateRangeException;

trait ValidateDateRangeTrait
{
    /**
     * @throws InvalidDateRangeException
     */
    private function validateDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): void
    {
        $utcTimezone = new \DateTimeZone('UTC');
        $startTimestamp = $startDate->setTimezone($utcTimezone)->getTimestamp();
        $endTimestamp = $endDate->setTimezone($utcTimezone)->getTimestamp();

        if ($endTimestamp <= $startTimestamp) {
            throw new InvalidDateRangeException('The end date should be greater than the start date.');
        }
    }
}