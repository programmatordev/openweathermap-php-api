<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Assistant;

use ProgrammatorDev\OpenWeatherMap\Helper\EntityHelper;

class Answer
{
    private string $answer;

    private string $sessionId;

    /** @var WeatherData[] */
    private array $data = [];

    public function __construct(array $data)
    {
        $this->answer = $data['answer'];
        $this->sessionId = $data['session_id'];

        if (!empty($data['data'])) {
            $this->data = EntityHelper::createEntityKeyList(WeatherData::class, $data['data']);
        }
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getData(): array
    {
        return $this->data;
    }
}