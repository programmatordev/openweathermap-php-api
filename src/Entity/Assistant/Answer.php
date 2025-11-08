<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity\Assistant;

use ProgrammatorDev\OpenWeatherMap\Util\EntityTrait;

class Answer
{
    use EntityTrait;

    private string $answer;

    private string $sessionId;

    /** @var WeatherData[] */
    private array $data = [];

    public function __construct(array $data)
    {
        $this->answer = $data['answer'];
        $this->sessionId = $data['session_id'];

        if (!empty($data['data'])) {
            $this->data = $this->createEntityKeyList(WeatherData::class, $data['data']);
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