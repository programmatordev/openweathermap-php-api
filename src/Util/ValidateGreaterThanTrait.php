<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateGreaterThanTrait
{
    private function validateGreaterThan(
        string $name,
        \DateTimeImmutable|int|float $value,
        \DateTimeImmutable|int|float $constraint
    ): void
    {
        if ($value instanceof \DateTimeImmutable && !$constraint instanceof \DateTimeImmutable) {
            throw new \LogicException('Constraint should be of type \DateTimeImmutable.');
        }

        if (!$value instanceof \DateTimeImmutable && $constraint instanceof \DateTimeImmutable) {
            throw new \LogicException('Constraint should be of type int|float.');
        }

        if ($value instanceof \DateTimeImmutable) {
            if ($value->getTimestamp() <= $constraint->getTimestamp()) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new \UnexpectedValueException(
                    \sprintf(
                        'The "%s" value "%s" is invalid. Must be greater than "%s".',
                        $name,
                        $value->format($dateFormat),
                        $constraint->format($dateFormat)
                    )
                );
            }
        }

        if ($value <= $constraint) {
            throw new \UnexpectedValueException(
                \sprintf(
                    'The "%s" value "%d" is invalid. Must be greater than "%d".',
                    $name,
                    $value,
                    $constraint
                )
            );
        }
    }
}