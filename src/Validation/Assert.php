<?php

namespace ProgrammatorDev\OpenWeatherMap\Validation;

final class Assert
{
    private function __construct() {}

    public static function notBlank(string $value, string $name): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new \InvalidArgumentException(sprintf(
                'The %s must be a non-empty string.',
                $name,
            ));
        }

        return $value;
    }

    public static function latitude(float $latitude): float
    {
        if (!is_finite($latitude) || $latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException(
                'Latitude must be a finite number between -90 and 90.',
            );
        }

        return $latitude;
    }

    public static function longitude(float $longitude): float
    {
        if (!is_finite($longitude) || $longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException(
                'Longitude must be a finite number between -180 and 180.',
            );
        }

        return $longitude;
    }

    public static function countryCode(string $countryCode): string
    {
        $countryCode = strtoupper(trim($countryCode));

        if (preg_match('/^[A-Z]{2}$/D', $countryCode) !== 1) {
            throw new \InvalidArgumentException(
                'The country code must contain exactly two ASCII letters.',
            );
        }

        return $countryCode;
    }

    public static function chronologicalRange(
        \DateTimeInterface $start,
        \DateTimeInterface $end,
    ): void {
        if ($end->getTimestamp() < $start->getTimestamp()) {
            throw new \InvalidArgumentException(
                'The end date must be after or equal to the start date.',
            );
        }
    }

    public static function notFuture(
        \DateTimeInterface $value,
        string $name,
    ): \DateTimeInterface {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException(sprintf(
                'The %s must not be in the future.',
                $name,
            ));
        }

        return $value;
    }

    public static function positiveInteger(int $value, string $name): int
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(sprintf(
                'The %s must be at least 1.',
                $name,
            ));
        }

        return $value;
    }

    public static function nonNegativeInteger(int $value, string $name): int
    {
        if ($value < 0) {
            throw new \InvalidArgumentException(sprintf(
                'The %s must be zero or greater.',
                $name,
            ));
        }

        return $value;
    }

    public static function integerBetween(
        int $value,
        int $minimum,
        int $maximum,
        string $name,
    ): int {
        if ($value < $minimum || $value > $maximum) {
            throw new \InvalidArgumentException(sprintf(
                'The %s must be between %d and %d.',
                $name,
                $minimum,
                $maximum,
            ));
        }

        return $value;
    }

    public static function tileCoordinate(
        int $coordinate,
        int $zoom,
        string $axis,
    ): int {
        $zoom = self::nonNegativeInteger($zoom, 'tile zoom level');

        // Each zoom level doubles the number of tiles along both axes, making
        // the valid zero-based coordinate range 0 through 2^zoom - 1.
        $maximum = (2 ** $zoom) - 1;

        if ($coordinate < 0 || $coordinate > $maximum) {
            if ($maximum === 0) {
                throw new \InvalidArgumentException(sprintf(
                    'At zoom level 0, the tile %s coordinate must be 0.',
                    strtoupper($axis),
                ));
            }

            throw new \InvalidArgumentException(sprintf(
                'At zoom level %d, the tile %s coordinate must be between 0 and %.0f.',
                $zoom,
                strtoupper($axis),
                $maximum,
            ));
        }

        return $coordinate;
    }
}
