<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidDateRangeException extends \Exception
{
    protected $message = 'The end date should be greater than the start date.';
}