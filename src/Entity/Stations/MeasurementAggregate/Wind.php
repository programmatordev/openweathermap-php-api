<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Wind implements EntityInterface
{
    private function __construct(
        private readonly ?float $direction,
        private readonly ?float $speed,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            direction: $reader->nullableFloat('deg'),
            speed: $reader->nullableFloat('speed'),
        );
    }

    public function direction(): ?float
    {
        return $this->direction;
    }

    public function directionUnit(): Unit
    {
        return Unit::DEGREE;
    }

    public function directionWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->direction,
            $this->directionUnit(),
        );
    }

    public function speed(): ?float
    {
        return $this->speed;
    }

    public function speedUnit(): Unit
    {
        return Unit::METERS_PER_SECOND;
    }

    public function speedWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->speed, $this->speedUnit());
    }
}
