<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast\City;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast\Period;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class Forecast implements EntityInterface
{
    /**
     * @param list<Period> $periods
     */
    private function __construct(
        private readonly ?int $count,
        private readonly array $periods,
        private readonly ?City $city,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
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

        $city = $reader->nullableArray('city');

        return new self(
            count: $reader->nullableInt('cnt'),
            periods: $periods,
            city: $city === null ? null : City::fromArray($city, $context),
        );
    }

    public function count(): ?int
    {
        return $this->count;
    }

    /**
     * @return list<Period>
     */
    public function periods(): array
    {
        return $this->periods;
    }

    public function city(): ?City
    {
        return $this->city;
    }
}
