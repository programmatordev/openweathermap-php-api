<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Weather;

use ProgrammatorDev\OpenWeatherMap\Entity\Condition;
use ProgrammatorDev\OpenWeatherMap\Entity\Wind;
use ProgrammatorDev\OpenWeatherMap\Helper\EntityHelper;

class WeatherData
{
    private \DateTimeImmutable $dateTime;

    private float $temperature;

    private float $temperatureFeelsLike;

    private float $minTemperature;

    private float $maxTemperature;

    private int $humidity;

    private int $cloudiness;

    private ?int $visibility;

    private int $atmosphericPressure;

    /** @var Condition[] */
    private array $conditions;

    private Wind $wind;

    private ?int $precipitationProbability;

    private ?float $rainVolume;

    private ?float $snowVolume;

    public function __construct(array $data)
    {
        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt']);
        $this->temperature = $data['main']['temp'];
        $this->temperatureFeelsLike = $data['main']['feels_like'];
        $this->minTemperature = $data['main']['temp_min'];
        $this->maxTemperature = $data['main']['temp_max'];
        $this->humidity = $data['main']['humidity'];
        $this->cloudiness = $data['clouds']['all'];
        $this->visibility = $data['visibility'] ?? null;
        $this->atmosphericPressure = $data['main']['pressure'];
        $this->conditions = EntityHelper::createEntityList(Condition::class, $data['weather']);
        $this->wind = new Wind($data['wind']);
        $this->precipitationProbability = isset($data['pop']) ? round($data['pop'] * 100) : null;
        $this->rainVolume = $data['rain']['1h'] ?? $data['rain']['3h'] ?? null;
        $this->snowVolume = $data['snow']['1h'] ?? $data['snow']['3h'] ?? null;
    }

    /**
     * DateTime in UTC
     */
    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }

    public function getTemperatureFeelsLike(): float
    {
        return $this->temperatureFeelsLike;
    }

    public function getMinTemperature(): float
    {
        return $this->minTemperature;
    }

    public function getMaxTemperature(): float
    {
        return $this->maxTemperature;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getCloudiness(): int
    {
        return $this->cloudiness;
    }

    /**
     * Visibility, meters
     * Maximum value is 10000
     */
    public function getVisibility(): ?int
    {
        return $this->visibility;
    }

    /**
     * Atmospheric pressure on the sea level, hPa
     */
    public function getAtmosphericPressure(): int
    {
        return $this->atmosphericPressure;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getWind(): Wind
    {
        return $this->wind;
    }

    public function getPrecipitationProbability(): ?int
    {
        return $this->precipitationProbability;
    }

    /**
     * Rain volume, mm
     */
    public function getRainVolume(): ?float
    {
        return $this->rainVolume;
    }

    /**
     * Snow volume, mm
     */
    public function getSnowVolume(): ?float
    {
        return $this->snowVolume;
    }
}