<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

trait BetweenValidatorTrait
{
    private function ValidateBetween(
        string $name,
        \DateTimeInterface|int|float $value,
        \DateTimeInterface|int|float $startConstraint,
        \DateTimeInterface|int|float $endConstraint
    ): void
    {
        if (
            $value instanceof \DateTimeInterface
            && (!$startConstraint instanceof \DateTimeInterface || !$endConstraint instanceof \DateTimeInterface)
        ) {
            throw new \LogicException('Both $startConstraint and $endConstraint should be of type \DateTimeInterface.');
        }

        if (
            !$value instanceof \DateTimeInterface
            && ($startConstraint instanceof \DateTimeInterface || $endConstraint instanceof \DateTimeInterface)
        ) {
            throw new \LogicException('Both $startConstraint and $endConstraint should be of type int|float.');
        }

        if ($value instanceof \DateTimeInterface) {
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