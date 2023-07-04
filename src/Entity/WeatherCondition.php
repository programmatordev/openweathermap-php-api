<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class WeatherCondition
{
    public const THUNDERSTORM = 'THUNDERSTORM';
    public const DRIZZLE = 'DRIZZLE';
    public const RAIN = 'RAIN';
    public const SNOW = 'SNOW';
    public const MIST = 'MIST';
    public const SMOKE = 'SMOKE';
    public const HAZE = 'HAZE';
    public const DUST = 'DUST';
    public const FOG = 'FOG';
    public const SAND = 'SAND';
    public const ASH = 'ASH';
    public const SQUALL = 'SQUALL';
    public const TORNADO = 'TORNADO';
    public const CLEAR = 'CLEAR';
    public const CLOUDS = 'CLOUDS';
    public const UNDEFINED = 'UNDEFINED';

    private int $id;

    private string $name;

    private string $description;

    private Icon $icon;

    private string $sysName;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['main'];
        $this->description = $data['description'];
        $this->icon = new Icon($data);
        $this->sysName = $this->findSysName($this->id);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIcon(): Icon
    {
        return $this->icon;
    }

    public function getSysName(): string
    {
        return $this->sysName;
    }

    /**
     * Find group based on this table https://openweathermap.org/weather-conditions
     */
    private function findSysName(int $id): string
    {
        return match ($id) {
            200, 201, 202, 210, 211, 212, 221, 230, 231, 232 => self::THUNDERSTORM,
            300, 301, 302, 310, 311, 312, 313, 314, 321 => self::DRIZZLE,
            500, 501, 502, 503, 504, 511, 520, 521, 522, 531 => self::RAIN,
            600, 601, 602, 611, 612, 613, 615, 616, 620, 621, 622 => self::SNOW,
            701 => self::MIST,
            711 => self::SMOKE,
            721 => self::HAZE,
            731, 761 => self::DUST,
            741 => self::FOG,
            751 => self::SAND,
            762 => self::ASH,
            771 => self::SQUALL,
            781 => self::TORNADO,
            800 => self::CLEAR,
            801, 802, 803, 804 => self::CLOUDS,
            default => self::UNDEFINED
        };
    }
}