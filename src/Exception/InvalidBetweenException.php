<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidBetweenException extends \UnexpectedValueException
{
    protected $message = 'The value is not between the expected values.';
}