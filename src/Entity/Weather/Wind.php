<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

final class Wind implements EntityInterface
{
    private function __construct(
        private readonly ?float $speed,
        private readonly ?int $direction,
        private readonly ?float $gust,
        private readonly Units $units,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            speed: $reader->nullableFloat('speed'),
            direction: $reader->nullableInt('deg'),
            gust: $reader->nullableFloat('gust'),
            units: UnitsResolver::fromContext($context),
        );
    }

    public function speed(): ?float
    {
        return $this->speed;
    }

    public function speedUnit(): Unit
    {
        return $this->units->speedUnit();
    }

    public function speedWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->speed, $this->speedUnit());
    }

    public function direction(): ?int
    {
        return $this->direction;
    }

    public function directionUnit(): Unit
    {
        return Unit::DEGREE;
    }

    public function directionWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->direction, $this->directionUnit());
    }

    public function gust(): ?float
    {
        return $this->gust;
    }

    public function gustUnit(): Unit
    {
        return $this->units->speedUnit();
    }

    public function gustWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->gust, $this->gustUnit());
    }
}
