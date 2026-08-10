<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Humidity implements EntityInterface
{
    private function __construct(
        private readonly ?float $average,
        private readonly ?int $weight,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            average: $reader->nullableFloat('average'),
            weight: $reader->nullableInt('weight'),
        );
    }

    public function average(): ?float
    {
        return $this->average;
    }

    public function averageUnit(): Unit
    {
        return Unit::PERCENT;
    }

    public function averageWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->average, $this->averageUnit());
    }

    public function weight(): ?int
    {
        return $this->weight;
    }
}
