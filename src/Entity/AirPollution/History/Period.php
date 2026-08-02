<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\History;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Components;
use ProgrammatorDev\OpenWeatherMap\Enum\AirQualityIndex;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Period implements EntityInterface
{
    private function __construct(
        private readonly ?\DateTimeImmutable $observedAt,
        private readonly ?AirQualityIndex $airQualityIndex,
        private readonly ?Components $components,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $airQualityIndex = $reader->nullableInt('main.aqi');

        if ($airQualityIndex !== null) {
            $airQualityIndex = AirQualityIndex::tryFrom($airQualityIndex)
                ?? throw HydrationException::invalidValue(
                    self::class,
                    'main.aqi',
                    'an integer from 1 through 5',
                    $airQualityIndex,
                );
        }

        $components = $reader->nullableArray('components');

        return new self(
            observedAt: $reader->nullableTimestamp('dt'),
            airQualityIndex: $airQualityIndex,
            components: $components === null
                ? null
                : Components::fromArray($components, $context),
        );
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
