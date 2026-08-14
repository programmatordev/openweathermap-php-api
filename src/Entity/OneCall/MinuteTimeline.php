<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\MinuteTimeline\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class MinuteTimeline implements EntityInterface
{
    /**
     * @param list<Period> $periods
     */
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly ?Timezone $timezone,
        private readonly array $periods,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $periods = [];

        foreach ($reader->nullableArray('data') ?? [] as $index => $period) {
            if (!is_array($period)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('data.%s', $index),
                    'array',
                    $period,
                );
            }

            $periods[] = Period::fromArray($period, $context);
        }

        $hasCoordinates = array_key_exists('lat', $data)
            || array_key_exists('lon', $data);
        $hasTimezone = array_key_exists('timezone', $data)
            || array_key_exists('timezone_offset', $data);

        return new self(
            coordinates: $hasCoordinates ? Coordinates::fromArray($data, $context) : null,
            timezone: $hasTimezone ? Timezone::fromArray($data, $context) : null,
            periods: $periods,
        );
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    public function timezone(): ?Timezone
    {
        return $this->timezone;
    }

    /**
     * @return list<Period>
     */
    public function periods(): array
    {
        return $this->periods;
    }
}
