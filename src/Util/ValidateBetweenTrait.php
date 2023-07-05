<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateBetweenTrait
{
    private function ValidateBetween(
        string $name,
        \DateTimeImmutable|int|float $value,
        \DateTimeImmutable|int|float $startConstraint,
        \DateTimeImmutable|int|float $endConstraint
    ): void
    {
        if (
            $value instanceof \DateTimeImmutable
            && (!$startConstraint instanceof \DateTimeImmutable || !$endConstraint instanceof \DateTimeImmutable)
        ) {
            throw new \LogicException('Both $startConstraint and $endConstraint should be of type \DateTimeImmutable.');
        }

        if (
            !$value instanceof \DateTimeImmutable
            && ($startConstraint instanceof \DateTimeImmutable || $endConstraint instanceof \DateTimeImmutable)
        ) {
            throw new \LogicException('Both $startConstraint and $endConstraint should be of type int|float.');
        }

        if ($value instanceof \DateTimeImmutable) {
            if ($value->getTimestamp() < $startConstraint->getTimestamp() || $value->getTimestamp() > $endConstraint->getTimestamp()) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new \UnexpectedValueException(
                    \sprintf(
                        'The "%s" date "%s" is invalid. Must be between "%s" and "%s".',
                        $name,
                        $value->format($dateFormat),
                        $startConstraint->format($dateFormat),
                        $endConstraint->format($dateFormat)
                    )
                );
            }
        }

        if ($value < $startConstraint || $value > $endConstraint) {
            throw new \UnexpectedValueException(
                \sprintf(
                    'The "%s" value "%d" is invalid. Must be between "%d" and "%d".',
                    $name,
                    $value,
                    $startConstraint,
                    $endConstraint
                )
            );
        }
    }
}