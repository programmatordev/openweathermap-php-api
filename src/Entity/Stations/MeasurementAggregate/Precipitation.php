<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Precipitation implements EntityInterface
{
    private function __construct(
        private readonly ?float $rain,
        private readonly ?float $snow,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            rain: $reader->nullableFloat('rain'),
            snow: $reader->nullableFloat('snow'),
        );
    }

    public function rain(): ?float
    {
        return $this->rain;
    }

    public function rainUnit(): Unit
    {
        return Unit::MILLIMETER;
    }

    public function rainWithUnit(): ?string
    {
        return $this->format($this->rain);
    }

    public function snow(): ?float
    {
        return $this->snow;
    }

    public function snowUnit(): Unit
    {
        return Unit::MILLIMETER;
    }

    public function snowWithUnit(): ?string
    {
        return $this->format($this->snow);
    }

    private function format(?float $precipitation): ?string
    {
        return MeasurementFormatter::format($precipitation, Unit::MILLIMETER);
    }
}
