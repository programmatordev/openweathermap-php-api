<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidBlankException extends \UnexpectedValueException
{
    protected $message = 'The value should not be blank.';
}