<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Coordinates;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Concern\HasWeatherMeasurements;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Current\Precipitation;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

final class Current implements EntityInterface
{
    use HasWeatherMeasurements;

    /**
     * @param list<Condition> $conditions
     */
    private function __construct(
        private readonly ?Coordinates $coordinates,
        private readonly array $conditions,
        private readonly ?float $temperature,
        private readonly ?float $feelsLikeTemperature,
        private readonly ?float $minimumTemperature,
        private readonly ?float $maximumTemperature,
        private readonly ?int $pressure,
        private readonly ?int $humidity,
        private readonly ?int $seaLevelPressure,
        private readonly ?int $groundLevelPressure,
        private readonly ?int $visibility,
        private readonly ?Wind $wind,
        private readonly ?Clouds $clouds,
        private readonly ?Precipitation $rain,
        private readonly ?Precipitation $snow,
        private readonly ?\DateTimeImmutable $observedAt,
        private readonly ?string $countryCode,
        private readonly ?\DateTimeImmutable $sunriseAt,
        private readonly ?\DateTimeImmutable $sunsetAt,
        private readonly ?int $timezoneOffset,
        private readonly ?int $id,
        private readonly ?string $name,
        private readonly Units $units,
    ) {}

    public static function fromArray(array $data, ?Context $context = null): static
    {
        $reader = PayloadReader::from($data, self::class);
        $coordinates = $reader->nullableArray('coord');
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

        $wind = $reader->nullableArray('wind');
        $clouds = $reader->nullableArray('clouds');
        $rain = $reader->nullableArray('rain');
        $snow = $reader->nullableArray('snow');

        return new self(
            coordinates: $coordinates === null
                ? null
                : Coordinates::fromArray($coordinates, $context),
            conditions: $conditions,
            temperature: $reader->nullableFloat('main.temp'),
            feelsLikeTemperature: $reader->nullableFloat('main.feels_like'),
            minimumTemperature: $reader->nullableFloat('main.temp_min'),
            maximumTemperature: $reader->nullableFloat('main.temp_max'),
            pressure: $reader->nullableInt('main.pressure'),
            humidity: $reader->nullableInt('main.humidity'),
            seaLevelPressure: $reader->nullableInt('main.sea_level'),
            groundLevelPressure: $reader->nullableInt('main.grnd_level'),
            visibility: $reader->nullableInt('visibility'),
            wind: $wind === null ? null : Wind::fromArray($wind, $context),
            clouds: $clouds === null ? null : Clouds::fromArray($clouds, $context),
            rain: $rain === null ? null : Precipitation::fromArray($rain, $context),
            snow: $snow === null ? null : Precipitation::fromArray($snow, $context),
            observedAt: $reader->nullableTimestamp('dt'),
            countryCode: $reader->nullableString('sys.country'),
            sunriseAt: $reader->nullableTimestamp('sys.sunrise'),
            sunsetAt: $reader->nullableTimestamp('sys.sunset'),
            timezoneOffset: $reader->nullableInt('timezone'),
            id: $reader->nullableInt('id'),
            name: $reader->nullableString('name'),
            units: UnitsResolver::fromContext($context),
        );
    }

    public function coordinates(): ?Coordinates
    {
        return $this->coordinates;
    }

    /**
     * @return list<Condition>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    public function wind(): ?Wind
    {
        return $this->wind;
    }

    public function clouds(): ?Clouds
    {
        return $this->clouds;
    }

    public function rain(): ?Precipitation
    {
        return $this->rain;
    }

    public function snow(): ?Precipitation
    {
        return $this->snow;
    }

    public function observedAt(): ?\DateTimeImmutable
    {
        return $this->observedAt;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function sunriseAt(): ?\DateTimeImmutable
    {
        return $this->sunriseAt;
    }

    public function sunsetAt(): ?\DateTimeImmutable
    {
        return $this->sunsetAt;
    }

    public function timezoneOffset(): ?int
    {
        return $this->timezoneOffset;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }
}
