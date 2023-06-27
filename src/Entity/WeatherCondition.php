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
        if ($id >= 200 && $id < 300) {
            return 'Thunderstorm';
        }
        else if ($id >= 300 && $id < 400) {
            return 'Drizzle';
        }
        else if ($id >= 500 && $id < 600) {
            return 'Rain';
        }
        else if ($id >= 600 && $id < 700) {
            return 'Snow';
        }
        else if ($id >= 700 && $id < 800) {
            return 'Atmosphere';
        }
        else if ($id === 800) {
            return 'Clear';
        }
        else if ($id >= 801 && $id < 810) {
            return 'Clouds';
        }

        return 'Undefined';
    }
}