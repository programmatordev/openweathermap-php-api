<?php

namespace ProgrammatorDev\OpenWeatherMap\Language;

class Language
{
    public const AFRIKAANS = 'af';
    public const ALBANIAN = 'al';
    public const ARABIC = 'ar';
    public const AZERBAIJANI = 'az';
    public const BASQUE = 'eu';
    public const BULGARIAN = 'bg';
    public const CATALAN = 'ca';
    public const CHINESE_SIMPLIFIED = 'zh_cn';
    public const CHINESE_TRADITIONAL = 'zh_tw';
    public const CROATIAN = 'hr';
    public const CZECH = 'cz';
    public const DANISH = 'da';
    public const DUTCH = 'nl';
    public const ENGLISH = 'en';
    public const FINNISH = 'fi';
    public const FRENCH = 'fr';
    public const GALICIAN = 'gl';
    public const GERMAN = 'de';
    public const GREEK = 'el';
    public const HINDI = 'hi';
    public const HEBREW = 'he';
    public const HUNGARIAN = 'hu';
    public const INDONESIAN = 'id';
    public const ITALIAN = 'it';
    public const JAPANESE = 'ja';
    public const KOREAN = 'kr';
    public const LATVIAN = 'la';
    public const LITHUANIAN = 'lt';
    public const MACEDONIAN = 'mk';
    public const NORWEGIAN = 'no';
    public const PERSIAN_FARSI = 'fa';
    public const POLISH = 'pl';
    public const PORTUGUESE = 'pt';
    public const PORTUGUESE_BRAZIL = 'pt_br';
    public const ROMANIAN = 'ro';
    public const RUSSIAN = 'ru';
    public const SERBIAN = 'sr';
    public const SLOVAK = 'sk';
    public const SLOVENIAN = 'sl';
    public const SPANISH = 'es';
    public const SWEDISH = 'sv';
    public const THAI = 'th';
    public const TURKISH = 'tr';
    public const UKRAINIAN = 'ua';
    public const VIETNAMESE = 'vi';
    public const ZULU = 'zu';

    public static function getList(): array
    {
        return [
            self::AFRIKAANS,
            self::ALBANIAN,
            self::ARABIC,
            self::AZERBAIJANI,
            self::BULGARIAN,
            self::CATALAN,
            self::CZECH,
            self::DANISH,
            self::GERMAN,
            self::GREEK,
            self::ENGLISH,
            self::SPANISH,
            self::BASQUE,
            self::PERSIAN_FARSI,
            self::FINNISH,
            self::FRENCH,
            self::GALICIAN,
            self::HEBREW,
            self::HINDI,
            self::CROATIAN,
            self::HUNGARIAN,
            self::INDONESIAN,
            self::ITALIAN,
            self::JAPANESE,
            self::KOREAN,
            self::LATVIAN,
            self::LITHUANIAN,
            self::MACEDONIAN,
            self::NORWEGIAN,
            self::DUTCH,
            self::POLISH,
            self::PORTUGUESE,
            self::PORTUGUESE_BRAZIL,
            self::ROMANIAN,
            self::RUSSIAN,
            self::SWEDISH,
            self::SLOVAK,
            self::SLOVENIAN,
            self::SERBIAN,
            self::THAI,
            self::TURKISH,
            self::UKRAINIAN,
            self::VIETNAMESE,
            self::CHINESE_SIMPLIFIED,
            self::CHINESE_TRADITIONAL,
            self::ZULU
        ];
    }
}