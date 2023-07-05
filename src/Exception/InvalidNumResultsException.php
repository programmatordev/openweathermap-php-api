<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidNumResultsException extends \UnexpectedValueException
{
    protected $message = 'The number of results value is invalid.';
}