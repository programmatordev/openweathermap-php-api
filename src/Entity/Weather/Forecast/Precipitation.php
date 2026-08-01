<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Precipitation implements EntityInterface
{
    private function __construct(
        private readonly ?float $lastThreeHours,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            lastThreeHours: $reader->nullableFloat('3h'),
        );
    }

    public function lastThreeHours(): ?float
    {
        return $this->lastThreeHours;
    }

    public function lastThreeHoursUnit(): Unit
    {
        return Unit::MILLIMETER;
    }

    public function lastThreeHoursWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->lastThreeHours,
            $this->lastThreeHoursUnit(),
        );
    }
}
