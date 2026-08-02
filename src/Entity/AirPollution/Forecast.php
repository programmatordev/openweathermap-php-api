<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\AirPollution;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\AirPollution\Forecast\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Forecast implements EntityInterface
{
    /**
     * @param list<Period> $periods
     */
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly array $periods,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $coordinates = $reader->nullableArray('coord');
        $periods = [];

        foreach ($reader->nullableArray('list') ?? [] as $index => $period) {
            if (!is_array($period)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('list.%s', $index),
                    'array',
                    $period,
                );
            }

            $periods[] = Period::fromArray($period, $context);
        }

        return new self(
            coordinates: $coordinates === null
                ? null
                : Coordinates::fromArray($coordinates, $context),
            periods: $periods,
        );
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return list<Period>
     */
    public function periods(): array
    {
        return $this->periods;
    }
}
