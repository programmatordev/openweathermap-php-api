<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidMeasurementSystemException extends \Exception
{
    protected $message = 'The measurement system value is invalid.';
}