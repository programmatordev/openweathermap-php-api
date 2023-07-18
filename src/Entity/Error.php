<?php

namespace ProgrammatorDev\OpenWeatherMap\Entity;

class Error
{
    private int $code;

    private string $message;

    private ?array $parameters;

    public function __construct(array $data)
    {
        $this->code = $data['cod'];
        $this->message = $data['message'];
        $this->parameters = $data['parameters'] ?? null;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }
}