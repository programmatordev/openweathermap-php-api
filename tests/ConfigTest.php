<?php

namespace ProgrammatorDev\OpenWeatherMap\Test;

use PHPUnit\Framework\Attributes\DataProvider;
use ProgrammatorDev\OpenWeatherMap\Config;
use ProgrammatorDev\OpenWeatherMap\HttpClient\HttpClientBuilder;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ConfigTest extends AbstractTest
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config([
            'applicationKey' => self::APPLICATION_KEY
        ]);
    }

    public function testConfigGetApplicationKey()
    {
        $this->assertSame('testappkey', $this->config->getApplicationKey());
    }

    public function testConfigGetHttpClientBuilder()
    {
        $this->assertInstanceOf(HttpClientBuilder::class, $this->config->getHttpClientBuilder());
    }

    public function testConfigGetUnit()
    {
        $this->assertSame('metric', $this->config->getUnit()); // Default value
    }

    public function testConfigGetUnitWithOptionValue()
    {
        $config = new Config([
            'applicationKey' => self::APPLICATION_KEY,
            'unit' => 'imperial'
        ]);

        $this->assertSame('imperial', $config->getUnit());
    }

    public function testConfigGetLanguage()
    {
        $this->assertSame('en', $this->config->getLanguage()); // Default value
    }

    public function testConfigGetLanguageWithOptionValue()
    {
        $config = new Config([
            'applicationKey' => self::APPLICATION_KEY,
            'language' => 'pt'
        ]);

        $this->assertSame('pt', $config->getLanguage());
    }

    #[DataProvider('provideInvalidConfigOptionsData')]
    public function testConfigWithInvalidOptions(array $options, string $expectedException)
    {
        $this->expectException($expectedException);

        new Config($options);
    }

    public static function provideInvalidConfigOptionsData(): \Generator
    {
        yield 'missing application key' => [
            [],
            MissingOptionsException::class
        ];
        yield 'empty application key' => [
            [
                'applicationKey' => ''
            ],
            InvalidOptionsException::class
        ];
        yield 'invalid unit' => [
            [
                'applicationKey' => self::APPLICATION_KEY,
                'unit' => 'invalid'
            ],
            InvalidOptionsException::class
        ];
        yield 'invalid language' => [
            [
                'applicationKey' => self::APPLICATION_KEY,
                'language' => 'invalid'
            ],
            InvalidOptionsException::class
        ];
    }
}