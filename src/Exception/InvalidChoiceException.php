<?php

namespace ProgrammatorDev\OpenWeatherMap\Exception;

class InvalidChoiceException extends \UnexpectedValueException
{
    protected $message = 'The given choice is not an accepted value.';
}