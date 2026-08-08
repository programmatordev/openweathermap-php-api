<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timezone;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

/**
 * @template TPeriod of EntityInterface
 * @template TPage of EntityInterface
 */
final class TimelinePage
{
    /**
     * @param list<TPeriod> $periods
     * @param Pagination<TPage> $pagination
     */
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly ?Timezone $timezone,
        private readonly array $periods,
        private readonly Pagination $pagination,
    ) {}

    /**
     * @template TPeriodClass of EntityInterface
     * @template TPageClass of EntityInterface
     *
     * @param class-string<TPageClass> $entity
     * @param class-string<TPeriodClass> $periodClass
     *
     * @return self<TPeriodClass, TPageClass>
     */
    public static function fromArray(
        array $data,
        string $entity,
        string $periodClass,
        ?Context $context = null,
    ): self {
        $reader = PayloadReader::from($data, $entity);
        $periods = [];

        foreach ($reader->nullableArray('data') ?? [] as $index => $period) {
            if (!is_array($period)) {
                throw HydrationException::invalidType(
                    $entity,
                    sprintf('data.%s', $index),
                    'array',
                    $period,
                );
            }

            $periods[] = $periodClass::fromArray($period, $context);
        }

        $hasCoordinates = array_key_exists('lat', $data)
            || array_key_exists('lon', $data);
        $hasTimezone = array_key_exists('timezone', $data)
            || array_key_exists('timezone_offset', $data);

        return new self(
            coordinates: $hasCoordinates ? Coordinates::fromArray($data, $context) : null,
            timezone: $hasTimezone ? Timezone::fromArray($data, $context) : null,
            periods: $periods,
            pagination: Pagination::fromArray(
                data: $data,
                pageClass: $entity,
                context: $context,
            ),
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
     * @return list<TPeriod>
     */
    public function periods(): array
    {
        return $this->periods;
    }

    /**
     * @return Pagination<TPage>
     */
    public function pagination(): Pagination
    {
        return $this->pagination;
    }
}
