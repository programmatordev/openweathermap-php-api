<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidPastDateException extends \UnexpectedValueException
{
    protected $message = 'The date should be in the past.';
}