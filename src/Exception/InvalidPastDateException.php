<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidPastDateException extends \Exception
{
    protected $message = 'The date should be in the past.';
}