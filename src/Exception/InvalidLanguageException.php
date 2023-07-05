<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidLanguageException extends \UnexpectedValueException
{
    protected $message = 'The language value is invalid.';
}