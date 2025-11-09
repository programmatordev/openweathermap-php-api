<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class ApiErrorException extends \Exception
{
    private ?array $parameters;

    public function __construct(array $error)
    {
        $code = $error['cod'] ?? $error['code'];

        parent::__construct($error['message'], $code);

        $this->parameters = $error['parameters'] ?? null;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }
}