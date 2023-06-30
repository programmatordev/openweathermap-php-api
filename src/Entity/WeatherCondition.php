<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class WeatherCondition
{
    private int $id;

    private string $group;

    private string $main;

    private string $description;

    private Icon $icon;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->group = $this->findGroup($this->id);
        $this->main = $data['main'];
        $this->description = $data['description'];
        $this->icon = new Icon($data);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getMain(): string
    {
        return $this->main;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIcon(): Icon
    {
        return $this->icon;
    }

    /**
     * Find group based on this table https://openweathermap.org/weather-conditions
     */
    private function findGroup(int $id): string
    {
        return match (true) {
            $id >= 200 && $id < 300 => 'Thunderstorm',
            $id >= 300 && $id < 400 => 'Drizzle',
            $id >= 500 && $id < 600 => 'Rain',
            $id >= 600 && $id < 700 => 'Snow',
            $id >= 700 && $id < 800 => 'Atmosphere',
            $id === 800 => 'Clear',
            $id >= 801 && $id < 810 => 'Clouds',
            default => 'Undefined'
        };
    }
}