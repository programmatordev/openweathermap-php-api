<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

final class HydrationException extends \UnexpectedValueException
{
    public static function invalidType(
        string $entity,
        string $path,
        string $expectedType,
        mixed $value
    ): self {
        return new self(sprintf(
            'Cannot hydrate %s: "%s" expected %s, %s received.',
            $entity,
            $path,
            $expectedType,
            get_debug_type($value)
        ));
    }

    public static function invalidValue(
        string $entity,
        string $path,
        string $expectedValue,
        int|string $value,
    ): self {
        return new self(sprintf(
            'Cannot hydrate %s: "%s" expected %s, "%s" received.',
            $entity,
            $path,
            $expectedValue,
            $value,
        ));
    }
}
