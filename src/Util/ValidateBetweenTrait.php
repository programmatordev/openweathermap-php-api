<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateBetweenTrait
{
    private function ValidateBetween(
        string $name,
        \DateTimeImmutable|int|float $value,
        \DateTimeImmutable|int|float $start,
        \DateTimeImmutable|int|float $end
    ): void
    {
        if (
            $value instanceof \DateTimeImmutable
            && (!$start instanceof \DateTimeImmutable || !$end instanceof \DateTimeImmutable)
        ) {
            throw new \LogicException('Both start and end should be of type \DateTimeImmutable.');
        }

        if (
            !$value instanceof \DateTimeImmutable
            && ($start instanceof \DateTimeImmutable || $end instanceof \DateTimeImmutable)
        ) {
            throw new \LogicException('Both start and end should be of type int|float.');
        }

        if ($value instanceof \DateTimeImmutable) {
            if ($value->getTimestamp() < $start->getTimestamp() || $value->getTimestamp() > $end->getTimestamp()) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new \UnexpectedValueException(
                    \sprintf(
                        'The "%s" date "%s" is invalid. Must be between "%s" and "%s".',
                        $name,
                        $value->format($dateFormat),
                        $start->format($dateFormat),
                        $end->format($dateFormat)
                    )
                );
            }
        }

        if ($value < $start || $value > $end) {
            throw new \UnexpectedValueException(
                \sprintf(
                    'The "%s" value "%d" is invalid. Must be between "%d" and "%d".',
                    $name,
                    $value,
                    $start,
                    $end
                )
            );
        }
    }
}