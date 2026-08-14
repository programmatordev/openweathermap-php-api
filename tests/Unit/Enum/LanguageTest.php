<?php

namespace ProgrammatorDev\OpenWeatherMap\Test\Unit\Enum;

use PHPUnit\Framework\TestCase;
use ProgrammatorDev\OpenWeatherMap\Enum\Language;

final class LanguageTest extends TestCase
{
    public function testItContainsTheDocumentedLanguageCodes(): void
    {
        $actual = [];

        foreach (Language::cases() as $language) {
            $actual[$language->name] = $language->value;
        }

        self::assertSame([
            'AFRIKAANS' => 'af',
            'ALBANIAN' => 'sq',
            'ARABIC' => 'ar',
            'AZERBAIJANI' => 'az',
            'BASQUE' => 'eu',
            'BELARUSIAN' => 'be',
            'BULGARIAN' => 'bg',
            'CATALAN' => 'ca',
            'CHINESE_SIMPLIFIED' => 'zh_cn',
            'CHINESE_TRADITIONAL' => 'zh_tw',
            'CROATIAN' => 'hr',
            'CZECH' => 'cz',
            'DANISH' => 'da',
            'DUTCH' => 'nl',
            'ENGLISH' => 'en',
            'FINNISH' => 'fi',
            'FRENCH' => 'fr',
            'GALICIAN' => 'gl',
            'GERMAN' => 'de',
            'GREEK' => 'el',
            'HEBREW' => 'he',
            'HINDI' => 'hi',
            'HUNGARIAN' => 'hu',
            'ICELANDIC' => 'is',
            'INDONESIAN' => 'id',
            'ITALIAN' => 'it',
            'JAPANESE' => 'ja',
            'KOREAN' => 'kr',
            'KURMANJI' => 'ku',
            'LATVIAN' => 'la',
            'LITHUANIAN' => 'lt',
            'MACEDONIAN' => 'mk',
            'NORWEGIAN' => 'no',
            'PERSIAN_FARSI' => 'fa',
            'POLISH' => 'pl',
            'PORTUGUESE' => 'pt',
            'PORTUGUESE_BRAZIL' => 'pt_br',
            'ROMANIAN' => 'ro',
            'RUSSIAN' => 'ru',
            'SERBIAN' => 'sr',
            'SLOVAK' => 'sk',
            'SLOVENIAN' => 'sl',
            'SPANISH' => 'es',
            'SPANISH_SP' => 'sp',
            'SWEDISH' => 'sv',
            'SWEDISH_SE' => 'se',
            'THAI' => 'th',
            'TURKISH' => 'tr',
            'UKRAINIAN' => 'uk',
            'UKRAINIAN_UA' => 'ua',
            'VIETNAMESE' => 'vi',
            'ZULU' => 'zu',
        ], $actual);
    }
}
