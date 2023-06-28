<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidLanguageException extends \Exception
{
    protected $message = 'The language value is invalid.';
}