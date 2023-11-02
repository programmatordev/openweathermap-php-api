<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\DataProvider;

use ProgrammatorDev\YetAnotherPhpValidator\Exception\ValidationException;

class InvalidParamDataProvider
{
    public static function provideInvalidUnitSystemData(): \Generator
    {
        yield 'not allowed unit system' => ['invalid', ValidationException::class];
    }

    public static function provideInvalidLanguageData(): \Generator
    {
        yield 'not allowed language' => ['invalid', ValidationException::class];
    }
}