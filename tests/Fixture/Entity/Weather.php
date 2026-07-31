<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Fixture\Entity;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\OpenWeatherMap;

final class Weather implements EntityInterface
{
    private function __construct(
        private readonly ?float $temperature,
        private readonly ?\DateTimeImmutable $observedAt,
        private readonly Units $units
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $payload = PayloadReader::from($data, self::class);

        return new self(
            temperature: $payload->nullableFloat('main.temp'),
            observedAt: $payload->nullableTimestamp('dt'),
            units: $context?->config()->get(OpenWeatherMap::OPTION_UNITS) ?? Units::METRIC
        );
    }

    public function temperature(): ?float
    {
        return $this->temperature;
    }

    public function observedAt(): ?\DateTimeImmutable
    {
        return $this->observedAt;
    }

    public function units(): Units
    {
        return $this->units;
    }
}
