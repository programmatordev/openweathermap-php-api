<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidApplicationKeyException extends \Exception
{
    protected $message = 'The application key value is invalid.';
}