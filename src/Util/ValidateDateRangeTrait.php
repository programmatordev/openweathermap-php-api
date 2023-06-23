<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateDateRangeTrait
{
    private function validateDateRange(\DateTimeImmutable $startDate, \DateTimeImmutable $endDate): void
    {
        $utcTimezone = new \DateTimeZone('UTC');
        $startTimestamp = $startDate->setTimezone($utcTimezone)->getTimestamp();
        $endTimestamp = $endDate->setTimezone($utcTimezone)->getTimestamp();

        if ($endTimestamp <= $startTimestamp) {
            throw new \InvalidArgumentException('The "endDate" should be greater than the "startDate".');
        }
    }
}