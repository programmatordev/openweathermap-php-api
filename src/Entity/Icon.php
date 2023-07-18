<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Icon
{
    private string $url = 'https://openweathermap.org/img/wn/%s@4x.png';

    private string $id;

    private string $imageUrl;

    public function __construct(array $data)
    {
        $this->id = $data['icon'];
        $this->imageUrl = \sprintf($this->url, $this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
}