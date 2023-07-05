<?php

namespace ProgrammatorDev\OpenWeatherMap\Util;

trait ValidateRangeTrait
{
    use ValidateLessThanTrait;

    public function validateRange(
        string $startName,
        string $endName,
        \DateTimeImmutable|int|float $startValue,
        \DateTimeImmutable|int|float $endValue
    ): void
    {
        if (
            ($startValue instanceof \DateTimeImmutable && !$endValue instanceof \DateTimeImmutable)
            || (!$startValue instanceof \DateTimeImmutable && $endValue instanceof \DateTimeImmutable)
        ) {
            throw new \LogicException('Both $startValue and $endValue should be of type \DateTimeImmutable or int|float.');
        }

        try {
            $this->validateLessThan('startValue', $startValue, $endValue);
        }
        catch (\UnexpectedValueException) {
            if ($startValue instanceof \DateTimeImmutable) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new \UnexpectedValueException(
                    \sprintf(
                        'The "%s" value "%s" should be less than the "%s" value "%s".',
                        $startName,
                        $startValue->format($dateFormat),
                        $endName,
                        $endValue->format($dateFormat)
                    )
                );
            }

            throw new \UnexpectedValueException(
                \sprintf(
                    'The "%s" value "%d" should be less than the "%s" value "%d".',
                    $startName,
                    $startValue,
                    $endName,
                    $endValue
                )
            );
        }
    }
}