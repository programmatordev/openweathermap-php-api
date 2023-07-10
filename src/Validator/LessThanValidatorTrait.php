<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

trait LessThanValidatorTrait
{
    /**
     * @throws ValidationException
     */
    private function validateLessThan(
        string $name,
        \DateTimeInterface|int|float $value,
        \DateTimeInterface|int|float $constraint
    ): void
    {
        if ($value instanceof \DateTimeInterface && !$constraint instanceof \DateTimeInterface) {
            throw new \LogicException('$constraint should be of type \DateTimeInterface.');
        }

        if (!$value instanceof \DateTimeInterface && $constraint instanceof \DateTimeInterface) {
            throw new \LogicException('$constraint should be of type int|float.');
        }

        if ($value instanceof \DateTimeInterface) {
            if ($value->getTimestamp() >= $constraint->getTimestamp()) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new ValidationException(
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
            throw new ValidationException(
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