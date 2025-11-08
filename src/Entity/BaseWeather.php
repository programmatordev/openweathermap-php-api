<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

use ProgrammatorDev\OpenWeatherMap\Util\EntityTrait;

class BaseWeather
{
    use EntityTrait;

    private \DateTimeImmutable $dateTime;

    private int $atmosphericPressure;

    private int $humidity;

    private float $dewPoint;

    private ?float $ultraVioletIndex;

    private int $cloudiness;

    private Wind $wind;

    /** @var Condition[] */
    private array $conditions;

    private ?float $rainVolume;

    private ?float $snowVolume;

    public function __construct(array $data)
    {
        $this->dateTime = \DateTimeImmutable::createFromFormat('U', $data['dt']);
        $this->atmosphericPressure = $data['pressure'];
        $this->humidity = $data['humidity'];
        $this->dewPoint = $data['dew_point'];
        $this->ultraVioletIndex = $data['uvi'] ?? null;
        $this->cloudiness = $data['clouds'];

        $this->wind = new Wind([
            'speed' => $data['wind_speed'],
            'deg' => $data['wind_deg'],
            'gust' => $data['wind_gust'] ?? null
        ]);

        $this->conditions = $this->createEntityList(Condition::class, $data['weather']);
        $this->rainVolume = $data['rain']['1h'] ?? $data['rain']['3h'] ?? $data['rain'] ?? null;
        $this->snowVolume = $data['snow']['1h'] ?? $data['snow']['3h'] ?? $data['snow'] ?? null;
    }

    /**
     * DateTime in UTC
     */
    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getAtmosphericPressure(): int
    {
        return $this->atmosphericPressure;
    }

    public function getHumidity(): int
    {
        return $this->humidity;
    }

    public function getDewPoint(): float
    {
        return $this->dewPoint;
    }

    public function getUltraVioletIndex(): ?float
    {
        return $this->ultraVioletIndex;
    }

    public function getCloudiness(): int
    {
        return $this->cloudiness;
    }

    public function getWind(): Wind
    {
        return $this->wind;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getRainVolume(): ?float
    {
        return $this->rainVolume;
    }

    public function getSnowVolume(): ?float
    {
        return $this->snowVolume;
    }
}