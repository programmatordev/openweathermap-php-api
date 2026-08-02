<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Current implements EntityInterface
{
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly ?\DateTimeImmutable $observedAt,
        private readonly ?AirQualityIndex $airQualityIndex,
        private readonly ?Components $components,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $airQualityIndex = $reader->nullableInt('list.0.main.aqi');

        if ($airQualityIndex !== null) {
            $airQualityIndex = AirQualityIndex::tryFrom($airQualityIndex)
                ?? throw HydrationException::invalidValue(
                    self::class,
                    'list.0.main.aqi',
                    'an integer from 1 through 5',
                    $airQualityIndex,
                );
        }

        $coordinates = $reader->nullableArray('coord');
        $components = $reader->nullableArray('list.0.components');

        return new self(
            coordinates: $coordinates === null
                ? null
                : Coordinates::fromArray($coordinates, $context),
            observedAt: $reader->nullableTimestamp('list.0.dt'),
            airQualityIndex: $airQualityIndex,
            components: $components === null
                ? null
                : Components::fromArray($components, $context),
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

    public function airQualityIndex(): ?AirQualityIndex
    {
        return $this->airQualityIndex;
    }

    public function components(): ?Components
    {
        return $this->components;
    }
}
