<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class ApiErrorException extends \Exception
{
    private ?array $parameters;

    public function __construct(string $message, int $code = 500, array $parameters = null, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->parameters = $parameters;
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }
}