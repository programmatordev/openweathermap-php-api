<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidNumResultsException extends \Exception
{
    protected $message = 'The number of results value is invalid.';
}