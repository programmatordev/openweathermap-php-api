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
        $timezoneUtc = new \DateTimeZone('UTC');
        $startTimestamp = $startDate->setTimezone($timezoneUtc)->getTimestamp();
        $endTimestamp = $endDate->setTimezone($timezoneUtc)->getTimestamp();

        if ($endTimestamp <= $startTimestamp) {
            throw new InvalidDateRangeException('The end date should be greater than the start date.');
        }
    }
}