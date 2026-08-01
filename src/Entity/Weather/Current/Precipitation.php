<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Current;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Precipitation implements EntityInterface
{
    private function __construct(
        private readonly ?float $lastHour,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            lastHour: $reader->nullableFloat('1h'),
        );
    }

    public function lastHour(): ?float
    {
        return $this->lastHour;
    }

    public function lastHourUnit(): Unit
    {
        // Current Weather documents the one-hour precipitation value as mm/h.
        // https://openweathermap.org/api/current
        return Unit::MILLIMETERS_PER_HOUR;
    }

    public function lastHourWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->lastHour, $this->lastHourUnit());
    }
}
