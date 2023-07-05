<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidDateRangeException extends \UnexpectedValueException
{
    protected $message = 'The end date should be greater than the start date.';
}