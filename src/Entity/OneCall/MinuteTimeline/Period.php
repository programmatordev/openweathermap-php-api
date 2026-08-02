<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Period implements EntityInterface
{
    /**
     * @param list<string> $alertIds
     */
    private function __construct(
        private readonly ?\DateTimeImmutable $forecastAt,
        private readonly ?float $precipitation,
        private readonly array $alertIds,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $alertIds = [];

        foreach ($reader->nullableArray('alerts') ?? [] as $index => $alertId) {
            if (!is_string($alertId)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('alerts.%s', $index),
                    'string',
                    $alertId,
                );
            }

            $alertIds[] = $alertId;
        }

        return new self(
            forecastAt: $reader->nullableTimestamp('dt'),
            precipitation: $reader->nullableFloat('precipitation'),
            alertIds: $alertIds,
        );
    }

    public function forecastAt(): ?\DateTimeImmutable
    {
        return $this->forecastAt;
    }

    public function precipitation(): ?float
    {
        return $this->precipitation;
    }

    public function precipitationUnit(): Unit
    {
        return Unit::MILLIMETERS_PER_HOUR;
    }

    public function precipitationWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->precipitation, $this->precipitationUnit());
    }

    /**
     * @return list<string>
     */
    public function alertIds(): array
    {
        return $this->alertIds;
    }
}
