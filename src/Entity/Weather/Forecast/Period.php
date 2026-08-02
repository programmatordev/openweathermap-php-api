<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather\Forecast;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Clouds;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Concern\HasWeatherMeasurements;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Condition;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Wind;
use ProgrammatorDev\OpenWeatherMap\Enum\PartOfDay;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

final class Period implements EntityInterface
{
    use HasWeatherMeasurements;

    /**
     * @param list<Condition> $conditions
     */
    private function __construct(
        private readonly ?\DateTimeImmutable $forecastAt,
        private readonly ?float $temperature,
        private readonly ?float $feelsLikeTemperature,
        private readonly ?float $minimumTemperature,
        private readonly ?float $maximumTemperature,
        private readonly ?int $pressure,
        private readonly ?int $humidity,
        private readonly ?int $seaLevelPressure,
        private readonly ?int $groundLevelPressure,
        private readonly ?float $dewPoint,
        private readonly array $conditions,
        private readonly ?Clouds $clouds,
        private readonly ?Wind $wind,
        private readonly ?int $visibility,
        private readonly ?float $precipitationProbability,
        private readonly ?Precipitation $rain,
        private readonly ?Precipitation $snow,
        private readonly ?PartOfDay $partOfDay,
        private readonly Units $units,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $conditions = [];

        foreach ($reader->nullableArray('weather') ?? [] as $index => $condition) {
            if (!is_array($condition)) {
                throw HydrationException::invalidType(
                    self::class,
                    sprintf('weather.%s', $index),
                    'array',
                    $condition,
                );
            }

            $conditions[] = Condition::fromArray($condition, $context);
        }

        $clouds = $reader->nullableArray('clouds');
        $wind = $reader->nullableArray('wind');
        $rain = $reader->nullableArray('rain');
        $snow = $reader->nullableArray('snow');
        $partOfDay = $reader->nullableString('sys.pod');

        if ($partOfDay !== null) {
            $partOfDay = PartOfDay::tryFrom($partOfDay)
                ?? throw HydrationException::invalidValue(
                    self::class,
                    'sys.pod',
                    '"d" or "n"',
                    $partOfDay,
                );
        }

        return new self(
            forecastAt: $reader->nullableTimestamp('dt'),
            temperature: $reader->nullableFloat('main.temp'),
            feelsLikeTemperature: $reader->nullableFloat('main.feels_like'),
            minimumTemperature: $reader->nullableFloat('main.temp_min'),
            maximumTemperature: $reader->nullableFloat('main.temp_max'),
            pressure: $reader->nullableInt('main.pressure'),
            humidity: $reader->nullableInt('main.humidity'),
            seaLevelPressure: $reader->nullableInt('main.sea_level'),
            groundLevelPressure: $reader->nullableInt('main.grnd_level'),
            // Captured 5 Day Forecast responses include dew_point even though its field table omits it.
            // The related Hourly Forecast contract documents the field and its unit behavior.
            // https://openweathermap.org/forecast5
            // https://openweathermap.org/api/hourly-forecast?collection=current_forecast
            dewPoint: $reader->nullableFloat('main.dew_point'),
            conditions: $conditions,
            clouds: $clouds === null ? null : Clouds::fromArray($clouds, $context),
            wind: $wind === null ? null : Wind::fromArray($wind, $context),
            visibility: $reader->nullableInt('visibility'),
            precipitationProbability: $reader->nullableFloat('pop'),
            rain: $rain === null ? null : Precipitation::fromArray($rain, $context),
            snow: $snow === null ? null : Precipitation::fromArray($snow, $context),
            partOfDay: $partOfDay,
            units: UnitsResolver::fromContext($context),
        );
    }

    public function forecastAt(): ?\DateTimeImmutable
    {
        return $this->forecastAt;
    }

    public function dewPoint(): ?float
    {
        return $this->dewPoint;
    }

    public function dewPointUnit(): Unit
    {
        return $this->temperatureUnit();
    }

    public function dewPointWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->dewPoint,
            $this->dewPointUnit(),
        );
    }

    /**
     * @return list<Condition>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    public function clouds(): ?Clouds
    {
        return $this->clouds;
    }

    public function wind(): ?Wind
    {
        return $this->wind;
    }

    public function precipitationProbability(): ?float
    {
        return $this->precipitationProbability;
    }

    public function rain(): ?Precipitation
    {
        return $this->rain;
    }

    public function snow(): ?Precipitation
    {
        return $this->snow;
    }

    public function partOfDay(): ?PartOfDay
    {
        return $this->partOfDay;
    }
}
