<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Concern;

use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;

trait HasPrecipitationProbability
{
    public function precipitationProbability(): ?float
    {
        return $this->precipitationProbability;
    }

    public function precipitationProbabilityUnit(): Unit
    {
        return Unit::PERCENT;
    }

    public function precipitationProbabilityWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->precipitationProbability,
            $this->precipitationProbabilityUnit(),
        );
    }

    private static function normalizePrecipitationProbability(
        ?float $probability,
    ): ?float {
        // OpenWeather returns a fraction from zero to one, while the public
        // getter presents the probability on the same percentage scale as
        // other percentage measurements.
        return $probability === null ? null : $probability * 100;
    }
}
