<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\Timeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Clouds;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Condition;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Current\Precipitation;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Wind;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

/**
 * Shared response shape used by the 15-minute and one-hour timelines.
 */
abstract class WeatherPeriod implements EntityInterface
{
    /**
     * @param list<Condition> $conditions
     * @param list<string> $alertIds
     */
    protected function __construct(
        private readonly ?\DateTimeImmutable $dateTime,
        private readonly ?float $temperature,
        private readonly ?float $feelsLikeTemperature,
        private readonly ?float $pressure,
        private readonly ?int $humidity,
        private readonly ?float $dewPointTemperature,
        private readonly ?float $ultravioletIndex,
        private readonly ?int $visibility,
        private readonly ?Wind $wind,
        private readonly ?Clouds $clouds,
        private readonly ?float $precipitationProbability,
        private readonly array $conditions,
        private readonly ?Precipitation $rain,
        private readonly ?Precipitation $snow,
        private readonly array $alertIds,
        private readonly Units $units,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, static::class);
        $conditions = [];

        foreach ($reader->nullableArray('weather') ?? [] as $index => $condition) {
            if (!is_array($condition)) {
                throw HydrationException::invalidType(
                    static::class,
                    sprintf('weather.%s', $index),
                    'array',
                    $condition,
                );
            }

            $conditions[] = Condition::fromArray($condition, $context);
        }

        $alertIds = [];

        foreach ($reader->nullableArray('alerts') ?? [] as $index => $alertId) {
            if (!is_string($alertId)) {
                throw HydrationException::invalidType(
                    static::class,
                    sprintf('alerts.%s', $index),
                    'string',
                    $alertId,
                );
            }

            $alertIds[] = $alertId;
        }

        $rain = $reader->nullableArray('rain');
        $snow = $reader->nullableArray('snow');
        $hasWind = array_key_exists('wind_speed', $data)
            || array_key_exists('wind_deg', $data)
            || array_key_exists('wind_gust', $data);
        $hasClouds = array_key_exists('clouds', $data);

        return new static(
            dateTime: $reader->nullableTimestamp('dt'),
            temperature: $reader->nullableFloat('temp'),
            feelsLikeTemperature: $reader->nullableFloat('feels_like'),
            pressure: $reader->nullableFloat('pressure'),
            humidity: $reader->nullableInt('humidity'),
            dewPointTemperature: $reader->nullableFloat('dew_point'),
            ultravioletIndex: $reader->nullableFloat('uvi'),
            visibility: $reader->nullableInt('visibility'),
            wind: $hasWind
                ? Wind::fromArray([
                    'speed' => $reader->nullableFloat('wind_speed'),
                    'deg' => $reader->nullableInt('wind_deg'),
                    'gust' => $reader->nullableFloat('wind_gust'),
                ], $context)
                : null,
            clouds: $hasClouds
                ? Clouds::fromArray([
                    'all' => $reader->nullableInt('clouds'),
                ], $context)
                : null,
            precipitationProbability: $reader->nullableFloat('pop'),
            conditions: $conditions,
            rain: $rain === null ? null : Precipitation::fromArray($rain, $context),
            snow: $snow === null ? null : Precipitation::fromArray($snow, $context),
            alertIds: $alertIds,
            units: UnitsResolver::fromContext($context),
        );
    }

    public function dateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function temperature(): ?float
    {
        return $this->temperature;
    }

    public function temperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function temperatureWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->temperature, $this->temperatureUnit());
    }

    public function feelsLikeTemperature(): ?float
    {
        return $this->feelsLikeTemperature;
    }

    public function feelsLikeTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function feelsLikeTemperatureWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->feelsLikeTemperature,
            $this->feelsLikeTemperatureUnit(),
        );
    }

    public function pressure(): ?float
    {
        return $this->pressure;
    }

    public function pressureUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function pressureWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->pressure, $this->pressureUnit());
    }

    public function humidity(): ?int
    {
        return $this->humidity;
    }

    public function humidityUnit(): Unit
    {
        return Unit::PERCENT;
    }

    public function humidityWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->humidity, $this->humidityUnit());
    }

    public function dewPointTemperature(): ?float
    {
        return $this->dewPointTemperature;
    }

    public function dewPointTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function dewPointTemperatureWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->dewPointTemperature,
            $this->dewPointTemperatureUnit(),
        );
    }

    public function ultravioletIndex(): ?float
    {
        return $this->ultravioletIndex;
    }

    public function visibility(): ?int
    {
        return $this->visibility;
    }

    public function visibilityUnit(): Unit
    {
        return Unit::METER;
    }

    public function visibilityWithUnit(): ?string
    {
        return MeasurementFormatter::format($this->visibility, $this->visibilityUnit());
    }

    public function wind(): ?Wind
    {
        return $this->wind;
    }

    public function clouds(): ?Clouds
    {
        return $this->clouds;
    }

    public function precipitationProbability(): ?float
    {
        return $this->precipitationProbability;
    }

    /**
     * @return list<Condition>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    public function rain(): ?Precipitation
    {
        return $this->rain;
    }

    public function snow(): ?Precipitation
    {
        return $this->snow;
    }

    /**
     * @return list<string>
     */
    public function alertIds(): array
    {
        return $this->alertIds;
    }
}
