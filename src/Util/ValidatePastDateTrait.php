<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidatePastDateTrait
{
    private function validatePastDate(string $name, \DateTimeImmutable $date): void
    {
        $utcTimezone = new \DateTimeZone('UTC');
        $nowTimestamp = (new \DateTimeImmutable('now', $utcTimezone))->getTimestamp();
        $dateTimestamp = $date->setTimezone($utcTimezone)->getTimestamp();

        if ($dateTimestamp > $nowTimestamp) {
            throw new \InvalidArgumentException(
                \sprintf('The "%s" should be a past date.', $name)
            );
        }
    }
}