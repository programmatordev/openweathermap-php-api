<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Clouds implements EntityInterface
{
    private function __construct(
        private readonly ?int $coverage,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        return new self(
            coverage: $reader->nullableInt('all'),
        );
    }

    public function coverage(): ?int
    {
        return $this->coverage;
    }

    public function coverageUnit(): Unit
    {
        return Unit::PERCENT;
    }

    public function coverageWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->coverage, $this->coverageUnit());
    }
}
