<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\OneCall\OneDayTimeline;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Clouds;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Condition;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Concern\HasPrecipitationProbability;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Wind;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

final class Period implements EntityInterface
{
    use HasPrecipitationProbability;

    /**
     * @param list<Condition> $conditions
     * @param list<string> $alertIds
     */
    private function __construct(
        private readonly ?\DateTimeImmutable $dateTime,
        private readonly ?\DateTimeImmutable $sunriseAt,
        private readonly ?\DateTimeImmutable $sunsetAt,
        private readonly ?\DateTimeImmutable $moonriseAt,
        private readonly ?\DateTimeImmutable $moonsetAt,
        private readonly ?float $moonPhase,
        private readonly ?Temperature $temperature,
        private readonly ?FeelsLikeTemperature $feelsLikeTemperature,
        private readonly ?float $pressure,
        private readonly ?int $humidity,
        private readonly ?float $dewPoint,
        private readonly ?float $ultravioletIndex,
        private readonly ?int $visibility,
        private readonly ?Wind $wind,
        private readonly ?Clouds $clouds,
        private readonly ?float $precipitationProbability,
        private readonly array $conditions,
        private readonly ?float $rain,
        private readonly ?float $snow,
        private readonly array $alertIds,
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

        $temperature = $reader->nullableArray('temp');
        $feelsLikeTemperature = $reader->nullableArray('feels_like');
        $hasWind = array_key_exists('wind_speed', $data)
            || array_key_exists('wind_deg', $data)
            || array_key_exists('wind_gust', $data);
        $hasClouds = array_key_exists('clouds', $data);

        return new self(
            dateTime: $reader->nullableTimestamp('dt'),
            sunriseAt: $reader->nullableTimestamp('sunrise'),
            sunsetAt: $reader->nullableTimestamp('sunset'),
            moonriseAt: $reader->nullableTimestamp('moonrise'),
            moonsetAt: $reader->nullableTimestamp('moonset'),
            moonPhase: $reader->nullableFloat('moon_phase'),
            temperature: $temperature === null
                ? null
                : Temperature::fromArray($temperature, $context),
            feelsLikeTemperature: $feelsLikeTemperature === null
                ? null
                : FeelsLikeTemperature::fromArray($feelsLikeTemperature, $context),
            pressure: $reader->nullableFloat('pressure'),
            humidity: $reader->nullableInt('humidity'),
            dewPoint: $reader->nullableFloat('dew_point'),
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
            precipitationProbability: self::normalizePrecipitationProbability(
                $reader->nullableFloat('pop'),
            ),
            conditions: $conditions,
            // Live daily responses return scalar precipitation, while the field table
            // still describes hourly objects: https://openweathermap.org/api/one-call-4
            rain: $reader->nullableFloat('rain'),
            snow: $reader->nullableFloat('snow'),
            alertIds: $reader->nullableStringList('alerts') ?? [],
            units: UnitsResolver::fromContext($context),
        );
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

    public function moonriseAt(): ?\DateTimeImmutable
    {
        return $this->moonriseAt;
    }

    public function moonsetAt(): ?\DateTimeImmutable
    {
        return $this->moonsetAt;
    }

    public function moonPhase(): ?float
    {
        return $this->moonPhase;
    }

    public function temperature(): ?Temperature
    {
        return $this->temperature;
    }

    public function feelsLikeTemperature(): ?FeelsLikeTemperature
    {
        return $this->feelsLikeTemperature;
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

    public function dewPoint(): ?float
    {
        return $this->dewPoint;
    }

    public function dewPointUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function dewPointWithUnit(): ?string
    {
        return MeasurementFormatter::format(
            $this->dewPoint,
            $this->dewPointUnit(),
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

    public function rain(): ?float
    {
        return $this->rain;
    }

    public function snow(): ?float
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
