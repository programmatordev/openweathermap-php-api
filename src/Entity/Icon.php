<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Icon
{
    private string $id;

    private string $url;

    public function __construct(array $data)
    {
        $this->id = $data['icon'];
        $this->url = sprintf('https://openweathermap.org/img/wn/%s@4x.png', $this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}