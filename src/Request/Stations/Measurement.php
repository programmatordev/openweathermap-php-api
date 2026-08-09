<?php

namespace ProgrammatorDev\OpenWeatherMap\Request\Stations;

use ProgrammatorDev\OpenWeatherMap\Validation\Assert;

final class Measurement
{
    private readonly \DateTimeImmutable $dateTime;

    private readonly ?float $temperature;

    private readonly ?float $windSpeed;

    private readonly ?float $windGust;

    private readonly ?int $windDirection;

    private readonly ?float $pressure;

    private readonly ?float $humidity;

    private readonly ?float $rainLastHour;

    private readonly ?float $rainLastSixHours;

    private readonly ?float $rainLastTwentyFourHours;

    private readonly ?float $snowLastHour;

    private readonly ?float $snowLastSixHours;

    private readonly ?float $snowLastTwentyFourHours;

    private readonly ?float $dewPoint;

    private readonly ?float $humidex;

    private readonly ?float $heatIndex;

    private readonly ?float $visibilityDistance;

    private readonly ?string $visibilityPrefix;

    /**
     * @var list<CloudLayer>
     */
    private readonly array $clouds;

    /**
     * @var list<Weather>
     */
    private readonly array $weather;

    /**
     * @param list<CloudLayer> $clouds
     * @param list<Weather> $weather
     */
    public function __construct(
        \DateTimeInterface $dateTime,
        ?float $temperature = null,
        ?float $windSpeed = null,
        ?float $windGust = null,
        ?int $windDirection = null,
        ?float $pressure = null,
        ?float $humidity = null,
        ?float $rainLastHour = null,
        ?float $rainLastSixHours = null,
        ?float $rainLastTwentyFourHours = null,
        ?float $snowLastHour = null,
        ?float $snowLastSixHours = null,
        ?float $snowLastTwentyFourHours = null,
        ?float $dewPoint = null,
        ?float $humidex = null,
        ?float $heatIndex = null,
        ?float $visibilityDistance = null,
        ?string $visibilityPrefix = null,
        array $clouds = [],
        array $weather = [],
    ) {
        $this->dateTime = \DateTimeImmutable::createFromInterface($dateTime)
            ->setTimezone(new \DateTimeZone('UTC'));
        $this->temperature = Assert::nullableFiniteNumber($temperature, 'temperature');
        $this->windSpeed = Assert::nullableFiniteNumber($windSpeed, 'wind speed');
        $this->windGust = Assert::nullableFiniteNumber($windGust, 'wind gust');
        $this->windDirection = $windDirection === null
            ? null
            : Assert::integerBetween($windDirection, 0, 360, 'wind direction');
        $this->pressure = Assert::nullableFiniteNumber($pressure, 'pressure');
        $this->humidity = Assert::nullableFiniteNumber($humidity, 'humidity');
        $this->rainLastHour = Assert::nullableFiniteNumber(
            $rainLastHour,
            'one-hour rainfall',
        );
        $this->rainLastSixHours = Assert::nullableFiniteNumber(
            $rainLastSixHours,
            'six-hour rainfall',
        );
        $this->rainLastTwentyFourHours = Assert::nullableFiniteNumber(
            $rainLastTwentyFourHours,
            '24-hour rainfall',
        );
        $this->snowLastHour = Assert::nullableFiniteNumber(
            $snowLastHour,
            'one-hour snowfall',
        );
        $this->snowLastSixHours = Assert::nullableFiniteNumber(
            $snowLastSixHours,
            'six-hour snowfall',
        );
        $this->snowLastTwentyFourHours = Assert::nullableFiniteNumber(
            $snowLastTwentyFourHours,
            '24-hour snowfall',
        );
        $this->dewPoint = Assert::nullableFiniteNumber($dewPoint, 'dew point');
        $this->humidex = Assert::nullableFiniteNumber($humidex, 'humidex');
        $this->heatIndex = Assert::nullableFiniteNumber($heatIndex, 'heat index');
        $this->visibilityDistance = Assert::nullableFiniteNumber(
            $visibilityDistance,
            'visibility distance',
        );
        $this->visibilityPrefix = Assert::nullableNotBlank(
            $visibilityPrefix,
            'visibility prefix',
        );
        $this->clouds = array_values(Assert::allInstancesOf(
            $clouds,
            CloudLayer::class,
            'cloud layer',
        ));
        $this->weather = array_values(Assert::allInstancesOf(
            $weather,
            Weather::class,
            'weather',
        ));
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function temperature(): ?float
    {
        return $this->temperature;
    }

    public function windSpeed(): ?float
    {
        return $this->windSpeed;
    }

    public function windGust(): ?float
    {
        return $this->windGust;
    }

    public function windDirection(): ?int
    {
        return $this->windDirection;
    }

    public function pressure(): ?float
    {
        return $this->pressure;
    }

    public function humidity(): ?float
    {
        return $this->humidity;
    }

    public function rainLastHour(): ?float
    {
        return $this->rainLastHour;
    }

    public function rainLastSixHours(): ?float
    {
        return $this->rainLastSixHours;
    }

    public function rainLastTwentyFourHours(): ?float
    {
        return $this->rainLastTwentyFourHours;
    }

    public function snowLastHour(): ?float
    {
        return $this->snowLastHour;
    }

    public function snowLastSixHours(): ?float
    {
        return $this->snowLastSixHours;
    }

    public function snowLastTwentyFourHours(): ?float
    {
        return $this->snowLastTwentyFourHours;
    }

    public function dewPoint(): ?float
    {
        return $this->dewPoint;
    }

    public function humidex(): ?float
    {
        return $this->humidex;
    }

    public function heatIndex(): ?float
    {
        return $this->heatIndex;
    }

    public function visibilityDistance(): ?float
    {
        return $this->visibilityDistance;
    }

    public function visibilityPrefix(): ?string
    {
        return $this->visibilityPrefix;
    }

    /**
     * @return list<CloudLayer>
     */
    public function clouds(): array
    {
        return $this->clouds;
    }

    /**
     * @return list<Weather>
     */
    public function weather(): array
    {
        return $this->weather;
    }

    /**
     * @return array<string, int|float|string|list<array<string, float|string>>>
     */
    public function toArray(): array
    {
        return array_filter([
            'dt' => $this->dateTime->getTimestamp(),
            'temperature' => $this->temperature,
            'wind_speed' => $this->windSpeed,
            'wind_gust' => $this->windGust,
            'wind_deg' => $this->windDirection,
            'pressure' => $this->pressure,
            'humidity' => $this->humidity,
            'rain_1h' => $this->rainLastHour,
            'rain_6h' => $this->rainLastSixHours,
            'rain_24h' => $this->rainLastTwentyFourHours,
            'snow_1h' => $this->snowLastHour,
            'snow_6h' => $this->snowLastSixHours,
            'snow_24h' => $this->snowLastTwentyFourHours,
            'dew_point' => $this->dewPoint,
            'humidex' => $this->humidex,
            'heat_index' => $this->heatIndex,
            'visibility_distance' => $this->visibilityDistance,
            'visibility_prefix' => $this->visibilityPrefix,
            'clouds' => $this->clouds === []
                ? null
                : array_map(
                    static fn(CloudLayer $cloud): array => $cloud->toArray(),
                    $this->clouds,
                ),
            'weather' => $this->weather === []
                ? null
                : array_map(
                    static fn(Weather $weather): array => $weather->toArray(),
                    $this->weather,
                ),
        ], static fn(mixed $value): bool => $value !== null);
    }
}
