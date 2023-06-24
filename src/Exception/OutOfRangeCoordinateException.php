<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class OutOfRangeCoordinateException extends \Exception
{
    protected $message = 'The coordinate latitude and/or longitude is out of range.';
}