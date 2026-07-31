<?php

namespace ProgrammatorDev\OpenWeatherMap\Value;

final class Coordinates
{
    private function __construct(
        private readonly float $latitude,
        private readonly float $longitude,
    ) {
        if (!is_finite($latitude) || $latitude < -90 || $latitude > 90) {
            throw new \InvalidArgumentException(
                'Latitude must be a finite number between -90 and 90.',
            );
        }

        if (!is_finite($longitude) || $longitude < -180 || $longitude > 180) {
            throw new \InvalidArgumentException(
                'Longitude must be a finite number between -180 and 180.',
            );
        }
    }

    public static function from(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    public function latitude(): float
    {
        return $this->latitude;
    }

    public function longitude(): float
    {
        return $this->longitude;
    }
}
