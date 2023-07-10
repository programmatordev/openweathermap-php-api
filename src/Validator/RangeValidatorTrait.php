<?php

namespace ProgrammatorDev\OpenWeatherMap\Validator;

use ProgrammatorDev\OpenWeatherMap\Exception\ValidationException;

trait RangeValidatorTrait
{
    use LessThanValidatorTrait;

    /**
     * @throws ValidationException
     */
    public function validateRange(
        string $startName,
        string $endName,
        \DateTimeInterface|int|float $startValue,
        \DateTimeInterface|int|float $endValue
    ): void
    {
        if (
            ($startValue instanceof \DateTimeInterface && !$endValue instanceof \DateTimeInterface)
            || (!$startValue instanceof \DateTimeInterface && $endValue instanceof \DateTimeInterface)
        ) {
            throw new \LogicException('Both $startValue and $endValue should be of type \DateTimeInterface or int|float.');
        }

        try {
            $this->validateLessThan('startValue', $startValue, $endValue);
        }
        catch (ValidationException) {
            if ($startValue instanceof \DateTimeInterface) {
                $dateFormat = 'Y-m-d H:i:s';

                throw new ValidationException(
                    \sprintf(
                        'The "%s" value "%s" should be less than the "%s" value "%s".',
                        $startName,
                        $startValue->format($dateFormat),
                        $endName,
                        $endValue->format($dateFormat)
                    )
                );
            }

            throw new ValidationException(
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