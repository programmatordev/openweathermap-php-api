<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Pressure implements EntityInterface
{
    private function __construct(
        private readonly ?float $minimum,
        private readonly ?float $maximum,
        private readonly ?float $average,
        private readonly ?int $weight,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            minimum: $reader->nullableFloat('min'),
            maximum: $reader->nullableFloat('max'),
            average: $reader->nullableFloat('average'),
            weight: $reader->nullableInt('weight'),
        );
    }

    public function minimum(): ?float
    {
        return $this->minimum;
    }

    public function minimumUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function minimumWithUnit(): ?string
    {
        return $this->format($this->minimum);
    }

    public function maximum(): ?float
    {
        return $this->maximum;
    }

    public function maximumUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function maximumWithUnit(): ?string
    {
        return $this->format($this->maximum);
    }

    public function average(): ?float
    {
        return $this->average;
    }

    public function averageUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function averageWithUnit(): ?string
    {
        return $this->format($this->average);
    }

    public function weight(): ?int
    {
        return $this->weight;
    }

    private function format(?float $pressure): ?string
    {
        return MeasurementFormatter::format($pressure, Unit::HECTOPASCAL);
    }
}
