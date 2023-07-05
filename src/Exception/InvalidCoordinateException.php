<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidCoordinateException extends \UnexpectedValueException
{
    protected $message = 'The coordinate latitude and/or longitude is invalid.';
}