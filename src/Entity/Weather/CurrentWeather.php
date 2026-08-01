<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\Api\Context\Context;
use ProgrammatorDev\Api\Contract\EntityInterface;
use ProgrammatorDev\OpenWeatherMap\Entity\Weather\Current\Precipitation;
use ProgrammatorDev\OpenWeatherMap\Enum\Unit;
use ProgrammatorDev\OpenWeatherMap\Enum\Units;
use ProgrammatorDev\OpenWeatherMap\Exception\HydrationException;
use ProgrammatorDev\OpenWeatherMap\Formatting\MeasurementFormatter;
use ProgrammatorDev\OpenWeatherMap\Hydration\PayloadReader;
use ProgrammatorDev\OpenWeatherMap\Hydration\UnitsResolver;

final class CurrentWeather implements EntityInterface
{
    /**
     * @param list<Condition> $conditions
     */
    private function __construct(
        private readonly ?float $latitude,
        private readonly ?float $longitude,
        private readonly array $conditions,
        private readonly ?string $base,
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
        private readonly ?int $systemType,
        private readonly ?int $systemId,
        private readonly ?string $countryCode,
        private readonly ?\DateTimeImmutable $sunriseAt,
        private readonly ?\DateTimeImmutable $sunsetAt,
        private readonly ?int $timezoneOffset,
        private readonly ?int $id,
        private readonly ?string $name,
        private readonly ?int $code,
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

        $wind = $reader->nullableArray('wind');
        $clouds = $reader->nullableArray('clouds');
        $rain = $reader->nullableArray('rain');
        $snow = $reader->nullableArray('snow');

        return new self(
            latitude: $reader->nullableFloat('coord.lat'),
            longitude: $reader->nullableFloat('coord.lon'),
            conditions: $conditions,
            base: $reader->nullableString('base'),
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
            systemType: $reader->nullableInt('sys.type'),
            systemId: $reader->nullableInt('sys.id'),
            countryCode: $reader->nullableString('sys.country'),
            sunriseAt: $reader->nullableTimestamp('sys.sunrise'),
            sunsetAt: $reader->nullableTimestamp('sys.sunset'),
            timezoneOffset: $reader->nullableInt('timezone'),
            id: $reader->nullableInt('id'),
            name: $reader->nullableString('name'),
            code: $reader->nullableInt('cod'),
            units: UnitsResolver::fromContext($context),
        );
    }

    public function latitude(): ?float
    {
        return $this->latitude;
    }

    public function longitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @return list<Condition>
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    public function base(): ?string
    {
        return $this->base;
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
        return $this->formatTemperature($this->temperature);
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
        return $this->formatTemperature($this->feelsLikeTemperature);
    }

    public function minimumTemperature(): ?float
    {
        return $this->minimumTemperature;
    }

    public function minimumTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function minimumTemperatureWithUnit(): ?string
    {
        return $this->formatTemperature($this->minimumTemperature);
    }

    public function maximumTemperature(): ?float
    {
        return $this->maximumTemperature;
    }

    public function maximumTemperatureUnit(): Unit
    {
        return $this->units->temperatureUnit();
    }

    public function maximumTemperatureWithUnit(): ?string
    {
        return $this->formatTemperature($this->maximumTemperature);
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
        return $this->formatPressure($this->pressure);
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

    public function seaLevelPressure(): ?int
    {
        return $this->seaLevelPressure;
    }

    public function seaLevelPressureUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function seaLevelPressureWithUnit(): ?string
    {
        return $this->formatPressure($this->seaLevelPressure);
    }

    public function groundLevelPressure(): ?int
    {
        return $this->groundLevelPressure;
    }

    public function groundLevelPressureUnit(): Unit
    {
        return Unit::HECTOPASCAL;
    }

    public function groundLevelPressureWithUnit(): ?string
    {
        return $this->formatPressure($this->groundLevelPressure);
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

    public function systemType(): ?int
    {
        return $this->systemType;
    }

    public function systemId(): ?int
    {
        return $this->systemId;
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

    public function code(): ?int
    {
        return $this->code;
    }

    private function formatTemperature(?float $temperature): ?string
    {
        return MeasurementFormatter::format($temperature, $this->units->temperatureUnit());
    }

    private function formatPressure(?int $pressure): ?string
    {
        return MeasurementFormatter::format($pressure, Unit::HECTOPASCAL);
    }
}
