<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class WithLanguageTest extends AbstractTest
{
    public function testWithLanguage()
    {
        $this->assertSame(
            'pt',
            $this->givenApi()->weather->withLanguage('pt')->getLanguage()
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidLanguageData')]
    public function testWithLanguageWithInvalidValue(string $language, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->weather->withLanguage($language);
    }
}