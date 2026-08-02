<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timezone;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\OneCallPaginationUrlNormalizer;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

/**
 * @template TPeriod of EntityInterface
 */
final class TimelinePage
{
    /**
     * @param list<TPeriod> $periods
     */
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly ?Timezone $timezone,
        private readonly array $periods,
        private readonly ?string $previousPageUrl,
        private readonly ?string $nextPageUrl,
    ) {}

    /**
     * @template T of EntityInterface
     *
     * @param class-string $entity
     * @param class-string<T> $periodClass
     *
     * @return self<T>
     */
    public static function fromArray(
        array $data,
        string $entity,
        string $endpointPath,
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
        $previousPageUrl = $reader->nullableString('prev');
        $nextPageUrl = $reader->nullableString('next');

        $previousPageUrl = $previousPageUrl === null
            ? null
            : OneCallPaginationUrlNormalizer::normalize(
                $previousPageUrl,
                $entity,
                'prev',
                $endpointPath,
            );
        $nextPageUrl = $nextPageUrl === null
            ? null
            : OneCallPaginationUrlNormalizer::normalize(
                $nextPageUrl,
                $entity,
                'next',
                $endpointPath,
            );

        return new self(
            coordinates: $hasCoordinates ? Coordinates::fromArray($data, $context) : null,
            timezone: $hasTimezone ? Timezone::fromArray($data, $context) : null,
            periods: $periods,
            previousPageUrl: $previousPageUrl,
            nextPageUrl: $nextPageUrl,
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

    public function previousPageUrl(): ?string
    {
        return $this->previousPageUrl;
    }

    public function nextPageUrl(): ?string
    {
        return $this->nextPageUrl;
    }
}
