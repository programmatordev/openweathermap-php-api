<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

use ProgrammatorDev\OpenWeatherMap\Entity\Error;

class ApiErrorException extends \Exception
{
    private ?array $parameters;

    public function __construct(Error $error, \Throwable $previous = null)
    {
        parent::__construct($error->getMessage(), $error->getCode(), $previous);

        $this->parameters = $error->getParameters();
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }
}