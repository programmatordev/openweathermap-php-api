<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidCoordinateException extends \Exception
{
    protected $message = 'The coordinate latitude and/or longitude is invalid.';
}