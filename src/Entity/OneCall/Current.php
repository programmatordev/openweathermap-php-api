<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
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

final class Current implements EntityInterface
{
    /**
     * @param list<Condition> $conditions
     * @param list<string> $alertIds
     */
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly ?Timezone $timezone,
        private readonly ?\DateTimeImmutable $dateTime,
        private readonly ?\DateTimeImmutable $sunriseAt,
        private readonly ?\DateTimeImmutable $sunsetAt,
        private readonly ?float $temperature,
        private readonly ?float $feelsLikeTemperature,
        private readonly ?int $pressure,
        private readonly ?int $humidity,
        private readonly ?float $dewPointTemperature,
        private readonly ?float $ultravioletIndex,
        private readonly ?int $visibility,
        private readonly ?Wind $wind,
        private readonly ?Clouds $clouds,
        private readonly array $conditions,
        private readonly ?Precipitation $rain,
        private readonly ?Precipitation $snow,
        private readonly array $alertIds,
        private readonly Units $units,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);

        // One Call wraps the single current observation in a one-item data list.
        $observation = $reader->nullableArray('data.0') ?? [];
        $conditions = [];

        foreach ($reader->nullableArray('data.0.weather') ?? [] as $index => $condition) {
            if (!is_array($condition)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('data.0.weather.%s', $index),
                    'array',
                    $condition,
                );
            }

            $conditions[] = Condition::fromArray($condition, $context);
        }

        $alertIds = [];

        foreach ($reader->nullableArray('data.0.alerts') ?? [] as $index => $alertId) {
            if (!is_string($alertId)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('data.0.alerts.%s', $index),
                    'string',
                    $alertId,
                );
            }

            $alertIds[] = $alertId;
        }

        $rain = $reader->nullableArray('data.0.rain');
        $snow = $reader->nullableArray('data.0.snow');
        $hasCoordinates = array_key_exists('lat', $data)
            || array_key_exists('lon', $data);
        $hasTimezone = array_key_exists('timezone', $data)
            || array_key_exists('timezone_offset', $data);
        $hasWind = array_key_exists('wind_speed', $observation)
            || array_key_exists('wind_deg', $observation)
            || array_key_exists('wind_gust', $observation);
        $hasClouds = array_key_exists('clouds', $observation);

        return new self(
            coordinates: $hasCoordinates ? Coordinates::fromArray($data, $context) : null,
            timezone: $hasTimezone ? Timezone::fromArray($data, $context) : null,
            dateTime: $reader->nullableTimestamp('data.0.dt'),
            sunriseAt: $reader->nullableTimestamp('data.0.sunrise'),
            sunsetAt: $reader->nullableTimestamp('data.0.sunset'),
            temperature: $reader->nullableFloat('data.0.temp'),
            feelsLikeTemperature: $reader->nullableFloat('data.0.feels_like'),
            pressure: $reader->nullableInt('data.0.pressure'),
            humidity: $reader->nullableInt('data.0.humidity'),
            dewPointTemperature: $reader->nullableFloat('data.0.dew_point'),
            ultravioletIndex: $reader->nullableFloat('data.0.uvi'),
            visibility: $reader->nullableInt('data.0.visibility'),
            wind: $hasWind
                ? Wind::fromArray([
                    'speed' => $reader->nullableFloat('data.0.wind_speed'),
                    'deg' => $reader->nullableInt('data.0.wind_deg'),
                    'gust' => $reader->nullableFloat('data.0.wind_gust'),
                ], $context)
                : null,
            clouds: $hasClouds
                ? Clouds::fromArray([
                    'all' => $reader->nullableInt('data.0.clouds'),
                ], $context)
                : null,
            conditions: $conditions,
            rain: $rain === null ? null : Precipitation::fromArray($rain, $context),
            snow: $snow === null ? null : Precipitation::fromArray($snow, $context),
            alertIds: $alertIds,
            units: UnitsResolver::fromContext($context),
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

    public function dateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function sunriseAt(): ?\DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    public function sunsetAt(): ?\DateTimeImmutable
    {
        return $this->sunsetAt;
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

    public function pressure(): ?int
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
