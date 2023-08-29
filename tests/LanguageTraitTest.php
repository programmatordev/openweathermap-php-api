<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use ProgrammatorDev\OpenWeatherMap\Test\DataProvider\InvalidParamDataProvider;

class LanguageTraitTest extends AbstractTest
{
    public function testLanguageTraitWithLanguage()
    {
        $this->assertSame(
            'pt',
            $this->givenApi()->weather()->withLanguage('pt')->getLanguage()
        );
    }

    #[DataProviderExternal(InvalidParamDataProvider::class, 'provideInvalidLanguageData')]
    public function testLanguageTraitWithLanguageWithInvalidValue(string $language, string $expectedException)
    {
        $this->expectException($expectedException);
        $this->givenApi()->weather()->withLanguage($language);
    }
}