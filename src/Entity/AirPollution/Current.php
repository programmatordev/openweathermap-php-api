<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Concern\HasAirQuality;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Current implements EntityInterface
{
    use HasAirQuality;

    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly ?\DateTimeImmutable $observedAt,
        private readonly AirQuality $airQuality,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $coordinates = $reader->nullableArray('coord');
        $observation = $reader->nullableArray('list.0') ?? [];

        return new self(
            coordinates: $coordinates === null
                ? null
                : Coordinates::fromArray($coordinates, $context),
            observedAt: $reader->nullableTimestamp('list.0.dt'),
            airQuality: AirQuality::fromArray($observation, $context),
        );
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function observedAt(): ?\DateTimeImmutable
    {
        return $this->observedAt;
    }
}
