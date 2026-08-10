<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Stations;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate\Humidity;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate\Precipitation;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate\Pressure;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate\Temperature;
use ProgrammatorDev\OpenWeatherMap\Entity\Stations\MeasurementAggregate\Wind;
use ProgrammatorDev\OpenWeatherMap\Enum\AggregationInterval;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;

final class MeasurementAggregate implements EntityInterface
{
    private function __construct(
        private readonly ?AggregationInterval $interval,
        private readonly ?\DateTimeImmutable $dateTime,
        private readonly ?string $stationId,
        private readonly ?Temperature $temperature,
        private readonly ?Humidity $humidity,
        private readonly ?Wind $wind,
        private readonly ?Pressure $pressure,
        private readonly ?Precipitation $precipitation,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $interval = $reader->nullableString('type');
        $temperature = $reader->nullableArray('temp');
        $humidity = $reader->nullableArray('humidity');
        $wind = $reader->nullableArray('wind');
        $pressure = $reader->nullableArray('pressure');
        $precipitation = $reader->nullableArray('precipitation');

        if ($interval !== null) {
            $interval = AggregationInterval::tryFrom($interval)
                ?? throw HydrationException::invalidValue(
                    self::class,
                    'type',
                    'one of m, h, or d',
                    $interval,
                );
        }

        return new self(
            interval: $interval,
            dateTime: $reader->nullableTimestamp('date'),
            stationId: $reader->nullableString('station_id'),
            temperature: $temperature === null
                ? null
                : Temperature::fromArray($temperature, $context),
            humidity: $humidity === null
                ? null
                : Humidity::fromArray($humidity, $context),
            wind: $wind === null ? null : Wind::fromArray($wind, $context),
            pressure: $pressure === null
                ? null
                : Pressure::fromArray($pressure, $context),
            precipitation: $precipitation === null
                ? null
                : Precipitation::fromArray($precipitation, $context),
        );
    }

    public function interval(): ?AggregationInterval
    {
        return $this->interval;
    }

    public function dateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function stationId(): ?string
    {
        return $this->stationId;
    }

    public function temperature(): ?Temperature
    {
        return $this->temperature;
    }

    public function humidity(): ?Humidity
    {
        return $this->humidity;
    }

    public function wind(): ?Wind
    {
        return $this->wind;
    }

    public function pressure(): ?Pressure
    {
        return $this->pressure;
    }

    public function precipitation(): ?Precipitation
    {
        return $this->precipitation;
    }
}
