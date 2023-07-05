<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

trait LessThanValidatorTrait
{
    private function validateLessThan(
        string $name,
        \DateTimeImmutable|int|float $value,
        \DateTimeImmutable|int|float $constraint
    ): void
    {
        if ($value instanceof \DateTimeImmutable && !$constraint instanceof \DateTimeImmutable) {
            throw new \LogicException('$constraint should be of type \DateTimeImmutable.');
        }

        if (!$value instanceof \DateTimeImmutable && $constraint instanceof \DateTimeImmutable) {
            throw new \LogicException('$constraint should be of type int|float.');
        }

        if ($value instanceof \DateTimeImmutable) {
            if ($value->getTimestamp() >= $constraint->getTimestamp()) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new \UnexpectedValueException(
                    \sprintf(
                        'The "%s" value "%s" is invalid. Must be less than "%s".',
                        $name,
                        $value->format($dateFormat),
                        $constraint->format($dateFormat)
                    )
                );
            }
        }

        if ($value >= $constraint) {
            throw new \UnexpectedValueException(
                \sprintf(
                    'The "%s" value "%d" is invalid. Must be less than "%d".',
                    $name,
                    $value,
                    $constraint
                )
            );
        }
    }
}