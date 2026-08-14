<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\DayTimeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

final class Temperature implements EntityInterface
{
    private function __construct(
        private readonly ?float $day,
        private readonly ?float $minimum,
        private readonly ?float $maximum,
        private readonly ?float $night,
        private readonly ?float $evening,
        private readonly ?float $morning,
        private readonly Units $units,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            day: $reader->nullableFloat('day'),
            minimum: $reader->nullableFloat('min'),
            maximum: $reader->nullableFloat('max'),
            night: $reader->nullableFloat('night'),
            evening: $reader->nullableFloat('eve'),
            morning: $reader->nullableFloat('morn'),
            units: UnitsResolver::fromContext($context),
        );
    }

    public function day(): ?float
    {
        return $this->day;
    }

    public function dayUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function dayWithUnit(): ?string
    {
        return $this->format($this->day);
    }

    public function minimum(): ?float
    {
        return $this->minimum;
    }

    public function minimumUnit(): Unit
    {
        return $this->units->temperatureUnit();
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
        return $this->units->temperatureUnit();
    }

    public function maximumWithUnit(): ?string
    {
        return $this->format($this->maximum);
    }

    public function night(): ?float
    {
        return $this->night;
    }

    public function nightUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function nightWithUnit(): ?string
    {
        return $this->format($this->night);
    }

    public function evening(): ?float
    {
        return $this->evening;
    }

    public function eveningUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function eveningWithUnit(): ?string
    {
        return $this->format($this->evening);
    }

    public function morning(): ?float
    {
        return $this->morning;
    }

    public function morningUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function morningWithUnit(): ?string
    {
        return $this->format($this->morning);
    }

    private function format(?float $temperature): ?string
    {
        return MeasurementFormatter::format($temperature, $this->units->temperatureUnit());
    }
}
